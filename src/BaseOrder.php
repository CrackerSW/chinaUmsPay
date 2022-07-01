<?php


namespace CrackerSw\ChinaUmsPay;


use CrackerSw\ChinaUmsPay\Exceptions\HttpException;
use  CrackerSw\ChinaUmsPay\Exceptions\InvalidArgumentException;

class BaseOrder extends ChinaUmsPay
{
    /**
     * 分账标记
     * @var bool
     */
    protected $divisionFlag = false;

    /**
     * 异步分账标记
     * @var bool
     */
    protected $asynDivisionFlag = false;

    /**
     * 分账信息
     * @var array
     */
    protected $subOrders;

    /**
     * 商品/分账信息
     * @var array
     */
    protected $goods;

    /**
     * 平台分账金额
     * @var array
     */
    protected $platformAmount;

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
     * @var array
     */
    protected $response;

    /**
     * @var array
     */
    protected $result;

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
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    protected function setSubOrder(array $subOrders): BaseOrder
    {
        $this->subOrders = $subOrders;
        return $this;
    }

    protected function setGoods(array $goods): BaseOrder
    {
        $this->goods = $goods;
        return $this;
    }

    protected function setDivisionFlag(bool $divisionFlag): BaseOrder
    {
        $this->divisionFlag = $divisionFlag;
        return $this;
    }

    protected function setAsynDivisionFlag(bool $asynDivisionFlag): BaseOrder
    {
        $this->asynDivisionFlag = $asynDivisionFlag;
        return $this;
    }

    /**
     * @throws HttpException
     */
    public function setResult(): void
    {
        if ($this->response && $this->response['errCode'] === "SUCCESS") {
            if ($this->response['errCode'] === "SUCCESS") {
                $this->result = $this->response;
            } else {
                throw new HttpException($this->response['errMsg']);
            }
        } else {
            throw new HttpException('无效的请求');
        }
    }

    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param string $uri
     * @param array $data
     * @param string $method
     * @return BaseOrder
     * @throws Exceptions\HttpException
     * @throws InvalidArgumentException
     */
    public function request(string $uri, array $data, string $method = 'POST'): BaseOrder
    {
        //开启分账
        if ($this->divisionFlag) {
            $data['divisionFlag'] = $this->divisionFlag;
        }

        //异步分账
        if ($this->asynDivisionFlag) {
            $data['asynDivisionFlag'] = $this->asynDivisionFlag;
        }

        //分账信息
        if ($this->divisionFlag || $this->asynDivisionFlag) {
            $data['goods'] = $this->goods ?: [];
            $data['subOrders'] = $this->subOrders ?: [];
            if (empty($data['goods']) && empty($data['subOrders'])) {
                throw new InvalidArgumentException('Goods and Suborders cannot at the same time is empty');
            }
        }

        $data = array_merge($data, $this->data);
        if ($this->need_data_tag) {
            $data = ['data' => $data];
        }
        info([__METHOD__, __LINE__, $uri, $data, $this->headers]);
        $this->response = $this->sendRequest($uri, $data, ['headers' => $this->headers], $method);
        $this->setResult();
        return $this;
    }
}