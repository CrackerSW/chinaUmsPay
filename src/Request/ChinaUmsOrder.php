<?php

namespace CrackerSw\ChinaUmsPay\Request;

use CrackerSw\ChinaUmsPay\ChinaUmsPay;
use CrackerSw\ChinaUmsPay\Exceptions\HttpException;
use CrackerSw\ChinaUmsPay\Exceptions\InvalidArgumentException;

class ChinaUmsOrder extends ChinaUmsPay
{
    /**
     * 微信小程序下单
     * @param $data
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function createUnifiedOrder($data)
    {
        $uri = '/netpay/wx/unified-order';
        $token = $this->getAccessToken();
    }

    /**
     * 微信app下单
     * @param $data
     */
    public function createWxAppOrder($data)
    {
        $uri = 'netpay/wx/app-pre-order';
    }

    /**
     * 支付宝app下单
     */
    public function createAliAppOrder()
    {
        $uri = '/netpay/trade/precreate';
    }

    /**
     * 云闪付小程序下单
     * @param $data
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function createUacMiniOrder($data)
    {
        $uri = '/netpay/uac/mini-order';
        $token = $this->getAccessToken();
    }

    /**
     * 云闪付小程序下单
     * @param $data
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function createUacAppOrder($data)
    {
        $uri = '/netpay/uac/app-order';
        $token = $this->getAccessToken();
    }


    /**
     * 订单查询
     * @param $data
     */
    public function orderQuery($data)
    {
        $uri = '/netpay/query';
    }

    /**
     * 退款
     */
    public function orderRefund()
    {
        $uri = '/netpay/refund' ;
    }

    /**
     * 退款查询
     * @param $data
     */
    public function orderRefundQuery($data)
    {
        $uri = '/netpay/refund-query' ;
    }

    /**
     * 订单关闭
     */
    public function orderClose()
    {
        $uri = '/netpay/close';
    }

    /**
     * 异步分账确认
     * @param $datas
     */
    public function subOrdersConfirm($datas)
    {
        $uri = '/netpay/sub-orders-confirm';
    }

//    public function

    /**
     * 发送请求
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(): array
    {
       //$this->sendRequest(self::SERVICE_CODE,$data);
    }
}
