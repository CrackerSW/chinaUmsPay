<?php

namespace CrackerSw\ChinaUmsPay;

use CrackerSw\ChinaUmsPay\Kernel\InteractsWithCache;
use CrackerSw\ChinaUmsPay\Exceptions\HttpException;
use CrackerSw\ChinaUmsPay\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;


class ChinaUmsPay
{

    use InteractsWithCache;

    const MINI_INST_MID = 'MINIDEFAULT';
    const APP_INST_MID = 'APPDEFAULT';
    const QRPAY_INST_MID = 'QRPAYDEFAULT';

    protected $guzzleOptions = [];

    protected $sign_method = "SHA256";
    protected $url;
    protected $debug = false;
    protected $version = "v1";
    protected $app_id;
    protected $app_key;
    protected $tid;
    protected $mid;
    protected $inst_mid;
    protected $msg_src_id;
    protected $need_token;
    protected $need_data_tag;
    protected $md5_key;


    public function __construct(array $config)
    {
        $this->debug = $config['debug'] ?? $this->debug;
        $this->version = $config['version'] ?? $this->version;
        $this->app_id = $config['app_id'] ?? $this->app_id;
        $this->app_key = $config['app_key'] ?? $this->app_key;
        $this->tid = $config['tid'] ?? $this->tid;
        $this->mid = $config['mid'] ?? $this->mid;
        $this->inst_mid = $config['inst_mid'] ?? $this->inst_mid;
        $this->msg_src_id = $config['msg_src_id'] ?? $this->msg_src_id;
        $this->need_token = $config['need_token'] ?? $this->need_token;
        $this->need_data_tag = $config['need_data_tag'] ?? $this->need_data_tag;
        $this->md5_key = $config['md5_key'] ?? $this->md5_key;
        if ($this->debug) {
            $this->url = "https://test-api-open.chinaums.com/" . $this->version; #测试地址
        } else {
            $this->url = "https://api-mop.chinaums.com/" . $this->version; #正式地址
        }

        $this->_initialize();
    }

    public function _initialize(): void
    {

    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
        return $this;
    }

    public static function getInstMid(int $pay_type): string
    {
        $instMids = [
            1 => self::MINI_INST_MID,
            2 => self::APP_INST_MID,
            3 => self::APP_INST_MID,
            4 => self::MINI_INST_MID,
            5 => self::APP_INST_MID,
        ];
        return $instMids[$pay_type] ?? $instMids[1];
    }


    public static function createUuid(string $prefix = ""): string
    {    //可以指定前缀
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str, 0, 8) . '-';
        $uuid .= substr($str, 8, 4) . '-';
        $uuid .= substr($str, 12, 4) . '-';
        $uuid .= substr($str, 16, 4) . '-';
        $uuid .= substr($str, 20, 12);
        return $prefix . $uuid;
    }

    /**
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function createMerOrderId(): string
    {
        $now = now('Asia/Shanghai');
        $cache = $this->getCache();
        while (!isset($mer_order_id) || $cache->has($mer_order_id)) {
            $micro = str_pad(substr($now->micro, 0, 3), 3, '0', STR_PAD_LEFT);
            $mer_order_id = $this->msg_src_id . $now->format('YmdHis') . $micro . self::randNum(7);
        }
        $cache->set($mer_order_id, $mer_order_id, 5);
        return $mer_order_id;
    }

    /**
     * @param int $length
     * @param int $type 1数字，2大小写字母，3大小写字母数字
     * @return string
     */
    public static function randNum(int $length = 10, int $type = 1): string
    {
        switch ($type) {
            case 2:
                $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 3:
                $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                break;
            case 1:
            default:
                $str = '0123456789';
                break;
        }
        //字符组合
        $len = strlen($str) - 1;
        $randStr = '';
        for ($i = 0; $i < $length; $i++) {
            $num = mt_rand(0, $len);
            $randStr .= $str[$num];
        }
        return $randStr;
    }

    /**
     * 获取TOKEN
     * @throws InvalidArgumentException
     * @throws HttpException
     */
    protected function getAccessToken($forceRefreshToken = false)
    {
        $cache = $this->getCache();
        if ($this->debug) {
            $cacheKey = "chinaumspay_token1_test_" . $this->app_id;
        } else {
            $cacheKey = "chinaumspay_token1_" . $this->app_id;  #正式地址
        }
        try {
            if (!$cache->has($cacheKey) || $forceRefreshToken) {
                $this->refreshAccessToken($cacheKey);
            }
            return $cache->get($cacheKey);
        } catch (\Psr\SimpleCache\InvalidArgumentException $exception) {
            throw new InvalidArgumentException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    protected function getOpenBodySig($body)
    {
        $appid = $this->app_id;
        $appkey = $this->app_key;
        $now = now('Asia/Shanghai');
        $timestamp = $now->format('YmdHis');
        $micro = $now->getPreciseTimestamp() / 1000000;
        $nonce = md5(uniqid($micro, true));
        $str = bin2hex(hash('sha256', $body, true));

        $signature = base64_encode(hash_hmac('sha256', "$appid$timestamp$nonce$str", $appkey, true));
        return "OPEN-BODY-SIG AppId=\"$appid\", Timestamp=\"$timestamp\", Nonce=\"$nonce\", Signature=\"$signature\"";

    }

    /**
     * @param string $cacheKey
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    private function refreshAccessToken(string $cacheKey): void
    {
        if (!$this->app_id || !$this->app_key) {
            throw new InvalidArgumentException('appId and appKey is not empty!');
        }
        $cache = $this->getCache();
        $nonce = self::createUuid();
        $now = now('Asia/Shanghai');
        $timestamp = $now->format('YmdHis');
        $data = [
            'appId' => $this->app_id,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'signMethod' => $this->sign_method,
            'signature' => hash($this->sign_method, $this->app_id . $timestamp . $nonce . $this->app_key),
        ];

        $response = $this->sendRequest("/token/access", $data);
//        info([__METHOD__, __LINE__, $response, $data]);

        if ($response['errCode'] !== '0000') {
            throw new InvalidArgumentException($response['errInfo'], $response['errCode']);
        }
        try {
            $cache->set($cacheKey, $response['accessToken'], 3300);
        } catch (\Psr\SimpleCache\InvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws HttpException
     */
    protected function sendRequest($uri, $data, $headers = [], $method = 'POST'): array
    {
        $url = $this->url . $uri;
        try {
            $response = $this->setGuzzleOptions($headers)
                ->getHttpClient()
                ->request($method, $url, [
                    'json' => $data
                ])->getBody()->getContents();
//            info([__METHOD__, __LINE__, $response]);
        } catch (GuzzleException $e) {
            info([__METHOD__, __LINE__, $data,$headers,[$e->getMessage(),$e->getCode()],$e]);

//            if (strpos($e->getMessage(), '认证失败') !== false) {
//                //token过期 强制刷新
//                try {
//                    if ($this->need_token) {
//                        $this->token = $this->getAccessToken(true);
//                        $this->headers['Authorization'] = 'OPEN-ACCESS-TOKEN AccessToken=' . $this->token;
//                    } else {
//                        $body=json_encode($data);
//                        $this->headers['Authorization'] = $this->getOpenBodySig($body);
//                    }
//
//                    /*                    usleep(100000);
//                                        $this->sendRequest($uri, $data, $headers, $method);*/
//                    $response = $this->setGuzzleOptions(['headers' => $this->headers])
//                        ->getHttpClient()
//                        ->request($method, $url, [
//                            'json' => $data
//                        ])->getBody()->getContents();
//                } catch (HttpException | InvalidArgumentException $exception) {
////                    info([__METHOD__, __LINE__, $data,$headers,[$exception->getMessage(),$exception->getCode()],$exception]);
//                    throw new HttpException($exception->getMessage(), $exception->getCode(), $exception);
//                }
//            } else {
                throw new HttpException($e->getMessage(), $e->getCode(), $e);
//            }

        }
        return json_decode($response, true) ?: [];
    }
}
