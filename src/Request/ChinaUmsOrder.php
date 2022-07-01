<?php

namespace CrackerSw\ChinaUmsPay\Request;

use CrackerSw\ChinaUmsPay\BaseOrder;
use CrackerSw\ChinaUmsPay\Exceptions\HttpException;
use CrackerSw\ChinaUmsPay\Exceptions\InvalidArgumentException;

class ChinaUmsOrder extends BaseOrder
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
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function createWxAppOrder(): array
    {
        $uri = '/netpay/wx/app-pre-order';
        $data['requestTimestamp'] = now()->format('Y-m-d H:i:s');
        $data['merOrderId'] = $this->createMerOrderId();
        $data['tradeType'] = 'APP';
        $data['totalAmount'] = 10;
        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
    }

    /**
     * 支付宝app下单
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     *
     * "data": {
    "connectSys": "UNIONPAY",
    "delegatedFlag": "N",
    "merName": "中保付测试商户(中保付测试商户)",
    "mid": "898201612345678",
    "appPayRequest": {
    "msgType": "trade.precreate",
    "qrCode": "https://qr.alipay.com/bax08021sblihexsp5305522"
    },
    "settleRefId": "01127600061N",
    "tid": "88880001",
    "totalAmount": 10,
    "qrCode": "https://qr.alipay.com/bax08021sblihexsp5305522",
    "targetMid": "2088510029762068",
    "responseTimestamp": "2022-07-01 13:43:52",
    "errCode": "SUCCESS",
    "targetStatus": "10000",
    "seqId": "01127600061N",
    "merOrderId": "103A202207011343523707956735", 103A202207011411206293888540
    "status": "NEW_ORDER",
    "targetSys": "Alipay 2.0"
    },
     */
    public function createAliAppOrder(): array
    {
        $uri = '/netpay/trade/precreate';
        $data['requestTimestamp'] = now()->format('Y-m-d H:i:s');
        $data['merOrderId'] = $this->createMerOrderId();
        $data['totalAmount'] = 10;
        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
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
