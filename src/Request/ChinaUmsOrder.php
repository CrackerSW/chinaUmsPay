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
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function createUnifiedOrder($data): array
    {
        $uri = '/netpay/wx/unified-order';
//        info([__METHOD__,__LINE__,$uri,$data]);
        $data['requestTimestamp'] = date('Y-m-d H:i:s');
        $data['merOrderId'] = $this->createMerOrderId();
        $data['tradeType'] = 'MINI';
        $data['instMid'] = self::getInstMid(1);
        return $this->request($uri,$data);
    }

    /**
     * 微信app下单
     * @param $data
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function createWxAppOrder($data): array
    {
        $uri = '/netpay/wx/app-pre-order';
        $data['requestTimestamp'] = date('Y-m-d H:i:s');
        $data['merOrderId'] = $this->createMerOrderId();
        $data['tradeType'] = 'APP';
        $data['instMid'] = self::getInstMid(2);
//        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
    }

    /**
     * 支付宝app下单
     * @param $data
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     *   * "data": {
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
     */
    public function createAliAppOrder($data): array
    {
//        $uri = '/netpay/trade/precreate';
        $uri = '/netpay/trade/app-pre-order';
        $data['requestTimestamp'] = date('Y-m-d H:i:s');
        $data['merOrderId'] = $this->createMerOrderId();
        $data['instMid'] = self::getInstMid(3);
//        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
    }

    /**
     * 云闪付小程序下单
     * @param $data
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function createUacMiniOrder($data): array
    {
        $uri = '/netpay/uac/mini-order';
        $data['requestTimestamp'] = date('Y-m-d H:i:s');
        $data['merOrderId'] = $this->createMerOrderId();
        $data['tradeType'] = 'MINI';
        $data['instMid'] = self::getInstMid(4);
//        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
    }

    /**
     * 云闪付小程序下单
     * @param $data
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function createUacAppOrder($data): array
    {
        $uri = '/netpay/uac/app-order';
        $data['requestTimestamp'] = date('Y-m-d H:i:s');
        $data['merOrderId'] = $this->createMerOrderId();
        $data['instMid'] = self::getInstMid(5);
//        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
    }


    /**
     * 订单查询
     * @param string $order_no
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     *   *   "payTime": "2022-07-01 13:43:51",
    "connectSys": "UNIONPAY",
    "delegatedFlag": "N",
    "errMsg": "交易不存在",
    "merName": "中保付测试商户(中保付测试商户)",
    "mid": "898201612345678",
    "settleDate": "2022-07-01",
    "settleRefId": "01127600061N",
    "tid": "88880001",
    "totalAmount": 10,
    "chnlCost": "1266000048020000",
    "targetMid": "2088510029762068",
    "responseTimestamp": "2022-07-01 15:30:21",
    "errCode": "SUCCESS",
    "targetStatus": "40004|ACQ.TRADE_NOT_EXIST",
    "seqId": "01127600061N",
    "merOrderId": "103A202207011343523707956735",
    "refundAmount": 0,
    "status": "NEW_ORDER",
    "targetSys": "Alipay 2.0"
     */
    public function orderQuery(array $data): array
    {
        $uri = '/netpay/query';
        $data['requestTimestamp'] = date('Y-m-d H:i:s');
//        $data['merOrderId'] = $order_no;
//        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
    }

    /**
     * 退款
     * @param array $data
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function orderRefund(array $data): array
    {
        $uri = '/netpay/refund' ;
        $data['requestTimestamp'] = date('Y-m-d H:i:s');
        $data['refundOrderId'] = $this->createMerOrderId();
//        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
    }

    /**
     * 退款查询
     * @param string $order_no
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function orderRefundQuery(array $data): array
    {
        $uri = '/netpay/refund-query';
        $data['requestTimestamp'] = date('Y-m-d H:i:s');
//        $data['merOrderId'] = $order_no;
//        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
    }

    /**
     * 订单关闭
     */
    public function orderClose($order_no): array
    {
        $uri = '/netpay/close';
        $data['requestTimestamp'] = date('Y-m-d H:i:s');
        $data['merOrderId'] = $order_no;
//        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
    }

    /**
     * 异步分账确认
     * @param $datas
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function subOrdersConfirm($datas): array
    {
        $uri = '/netpay/sub-orders-confirm';
        $data['requestTimestamp'] = date('Y-m-d H:i:s');
//        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
    }

    public function getToken()
    {
        return $this->token;
    }
}
