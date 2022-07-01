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
        $this->data['instMid'] = $this->inst_mid;
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
            if ($response['errCode'] === "SUCCESS") {
                return $response;
            }
            throw new HttpException($response['errMsg']);
        }
        throw new HttpException('无效的请求');
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

        $data = array_merge($data, $this->data);
        if ($this->need_data_tag) {
            $data = ['data' => $data];
        }
        info([__METHOD__, __LINE__, $uri, $data, $this->headers]);
        $response = $this->sendRequest($uri, $data, ['headers' => $this->headers], $method);
        return $this->getResult($response);
    }
}