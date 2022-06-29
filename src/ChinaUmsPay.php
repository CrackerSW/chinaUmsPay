<?php

namespace CrackerSw\chinaUmsPay;

use CrackerSw\ChinaUmsPay\Kernel\InteractsWithCache;
use CrackerSw\chinaUmsPay\Exceptions\HttpException;
use CrackerSw\chinaUmsPay\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;


class ChinaUmsPay
{

    use InteractsWithCache;

    protected $serviceCode;
    protected $apiMethodName;
    protected $guzzleOptions = [];

    protected $appDebug = false;
    protected $apiVersion = 'v1';
    protected $url = 'https://api-mop.chinaums.com/';
    protected $test_url = 'https://test-api-open.chinaums.com/';
    protected $appId = '';
    protected $appKey = '';
    protected $tid = '';
    protected $mid = '';
    protected $instMid = '';
    protected $needToken = true;
    protected $needDataTag = true;
    protected $signMethod = "SHA256";

    public function __construct(string $appId,string $appKey)
    {
        $this->key = $key;
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

    public function getAccessToken()
    {
        info([__METHOD__, __LINE__, '开始获取toekn']);
        $cache = $this->getCache();
        $cacheKey = "chinaumspay_token_".$this->appId;

        if ($cache->has($cacheKey)){
            return $cache->get($cacheKey);
        }
        $data = [
            'appId' => $this->appId,
            'timestamp' => now()->format('YmdHis'),
            'nonce' => self::createUuid(),
            'appKey' => $this->appKey,
        ];
        if (!$data['appId'] || !$data['appKey'] ) {
            throw new InvalidArgumentException('appId and appKey is not empty!');
        }
        $data['signature'] = hash($this->signMethod,implode('',$data));
        $data['signMethod'] = $this->signMethod;
        info([__METHOD__, __LINE__, '请求报文', $data]);
        try {
            if ($this->appDebug) {
                $url = $this->test_url . $this->apiVersion . "/token/access";
            } else {
                $url = $this->url . $this->apiVersion . "/token/access";
            }
            $response = $this->getHttpClient()->post($url, $data)->getBody()->getContents();
            $response = json_decode($response, true);
            if ($response['errCode'] !== '0000') {
                throw new InvalidArgumentException($response['errInfo'], $response['errCode']);
            }
            $cache->set($cacheKey,$response['accessToken'],'3300');
            return $response['accessToken'];
        } catch (\Exception $exception) {
            throw new HttpException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
