<?php

namespace CrackerSw\chinaUmsPay;

use CrackerSw\ChinaUmsPay\Kernel\InteractsWithCache;
use CrackerSw\ChinaUmsPay\Exceptions\HttpException;
use CrackerSw\ChinaUmsPay\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use function AlibabaCloud\Client\json;


class ChinaUmsPay
{

    use InteractsWithCache;

    protected $service_code;
    protected $api_method_name;
    protected $guzzleOptions = [];

    protected $sign_method = "SHA256";
    protected $url = 'https://api-mop.chinaums.com/';               #正式地址
    protected $test_url = 'https://test-api-open.chinaums.com/';    #测试地址
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
        $this->need_data_tag = $config['$need_data_tag'] ?? $this->need_data_tag;

    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
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
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getAccessToken()
    {
        info([__METHOD__, __LINE__, '开始获取toekn']);
        $cache = $this->getCache();
        $cacheKey = "chinaumspay_token_" . $this->app_id;

        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }
        $nonce = self::createUuid();
        $timestamp = now()->format('YmdHis');
        $data = [
            'appId' => $this->app_id,
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'signMethod' => $this->sign_method,
            'signature' => hash($this->sign_method, $this->app_id . $timestamp . $nonce . $this->app_key),
        ];
        if (!$this->app_id || !$this->app_key) {
            throw new InvalidArgumentException('appId and appKey is not empty!');
        }
        info([__METHOD__, __LINE__, '请求报文', $data]);
        try {
            if ($this->debug) {
                $url = $this->test_url . $this->version . "/token/access";
            } else {
                $url = $this->url . $this->version . "/token/access";
            }
            $response = Http::post($url, $data);
            $response = json_decode($response, true);
//            $response = [
//                'errCode' => "0000",
//                'errInfo' => "正常",
//                'accessToken' => "0beae96eb1004b8792b27279fed75ea5",
//                'expiresIn' => 3600,
//            ];
            if ($response['errCode'] !== '0000') {
                throw new InvalidArgumentException($response['errInfo'], $response['errCode']);
            }
            $cache->set($cacheKey, $response['accessToken'], 3300);
            return $response['accessToken'];
        } catch (\Exception $exception) {
            throw new HttpException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
