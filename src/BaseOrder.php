<?php


namespace CrackerSw\ChinaUmsPay;


use CrackerSw\ChinaUmsPay\Exceptions\HttpException;
use  CrackerSw\ChinaUmsPay\Exceptions\InvalidArgumentException;

class BaseOrder extends ChinaUmsPay
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var array
     */
    protected $data;


    /**
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidArgumentException
     */
    public function _initialize(): void
    {
        if ($this->need_token) {
            $this->token = $this->getAccessToken();
            $this->headers['Authorization'] = 'OPEN-ACCESS-TOKEN AccessToken=' . $this->token;
        }
        $this->data['tid'] = $this->tid;
        $this->data['mid'] = $this->mid;
//        $this->data['instMid'] = $this->inst_mid;
        $this->data['msgSrcId'] = $this->msg_src_id;
    }

    protected function setHeaders(array $headers): BaseOrder
    {
        if ($headers['Authorization']) {
            unset($headers['Authorization']);
        }
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * @throws HttpException
     */
    public function getResult($response): array
    {
        if ($response && $response['errCode'] === "SUCCESS") {
            return $response;
        }
        throw new HttpException("[{$response['errCode']}]: {$response['errMsg']}");
    }

    /**
     * @param string $uri
     * @param array $data
     * @param string $method
     * @return array
     * @throws Exceptions\HttpException
     * @throws InvalidArgumentException
     */
    public function request(string $uri, array $data, string $method = 'POST'): array
    {
        //分账信息
        if ((isset($data['divisionFlag']) && $data['divisionFlag']) || (isset($data['asynDivisionFlag']) && $data['asynDivisionFlag'])) {
            if (empty($data['goods']) && empty($data['subOrders'])) {
                throw new InvalidArgumentException('Goods and Suborders cannot at the same time is empty');
            }
        }

        if (isset($data['instMid']) &&$data['instMid']) {
            $this->data['instMid'] = $data['instMid'];
        }

        $data = array_merge($data, $this->data);
        if ($this->need_data_tag) {
            $data = ['data' => $data];
        }

        info([__METHOD__, __LINE__, $uri, $data, $this->headers]);
        $response = $this->sendRequest($uri, $data, ['headers' => $this->headers], $method);
        return $this->getResult($response);
    }

    /**
     * @param array $data
     * @return bool
     * @throws InvalidArgumentException
     */
    public function verifySign(array $data): bool
    {
        $sign = $data['sign'];
        unset($data['sign']);
        ksort($data);
        //reset()内部指针指向数组中的第⼀个元素
        reset($data);
        $buff = '';
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $v = json_encode($v,JSON_UNESCAPED_UNICODE);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $verify_sign = strtoupper(hash('sha256',rtrim($buff,'&').$this->md5_key));
        info([__METHOD__, __LINE__, $verify_sign, $sign,$data,$this->md5_key,$buff]);
        if ($verify_sign !== $sign) {
            throw new InvalidArgumentException('UMS签名错误',2005);
        }
        return true;
    }
}