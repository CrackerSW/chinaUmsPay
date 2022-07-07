<?php
namespace CrackerSw\ChinaUmsPay\Request;

use CrackerSw\ChinaUmsPay\BaseOrder;
use CrackerSw\ChinaUmsPay\Exceptions\HttpException;
use CrackerSw\ChinaUmsPay\Exceptions\InvalidArgumentException;

class ChinaUmsQrcodeOrder extends BaseOrder
{
    /**
     * 获取二维码
     * @param $data
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
    "data": {
    "qrCodeId": "147G2207075474017133708486",
    "errMsg": "查询二维码成功",
    "mid": "898325273921087",
    "msgId": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "billDate": "2022-07-07",
    "tid": "5KRD1FY2",
    "instMid": "QRPAYDEFAULT",
    "responseTimestamp": "2022-07-07 13:40:17",
    "errCode": "SUCCESS",
    "billNo": "147G202207071340172291962600",
    "billQRCode": "https://qr.95516.com/48020000/147G2207075474017133708486"
    },
     */
    public function getQrcode($data): array
    {
        $uri = '/netpay/bills/get-qrcode';
        $data['billNo'] = $this->createMerOrderId();
        $data['billDate'] = now()->format('Y-m-d H:i:s');
        $data['instMid'] = self::QRPAY_INST_MID;
        $data['requestTimestamp'] = now()->format('Y-m-d H:i:s');
        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
    }

    /**
     * 更新二维码
     * @param $data
     */
    public function updateQrcode($data)
    {
        $uri = '/netpay/bills/update-qrcode';
        $data['instMid'] = self::QRPAY_INST_MID;
        $data['requestTimestamp'] = now()->format('Y-m-d H:i:s');

    }

    /**
     * 关闭二维码
     */
    public function closeQrcode()
    {
        $uri = '/netpay/bills/close-qrcode';
        $data['instMid'] = self::QRPAY_INST_MID;
        $data['requestTimestamp'] = now()->format('Y-m-d H:i:s');
    }

    /**
     * 账单查询
     * @param string $order_no
     * @return array
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function billsQuery(array $data): array
    {
        $uri = '/netpay/bills/query';
        $data['instMid'] = self::QRPAY_INST_MID;
        $data['requestTimestamp'] = now()->format('Y-m-d H:i:s');
//        $data['billNo'] = $order_no;
        info([__METHOD__,__LINE__,$uri,$data]);
        return $this->request($uri,$data);
    }

    /**
     * 退款
     */
    public function billsRefund()
    {
        $uri = '/netpay/bills/refund' ;
        $data['instMid'] = self::QRPAY_INST_MID;
        $data['requestTimestamp'] = now()->format('Y-m-d H:i:s');
    }

    /**
     * 二维码信息查询
     * @param $data
     */
    public function queryQrcodeInfo($data)
    {
        $uri = '/netpay/bills/query-qrcode-info' ;
        $data['instMid'] = self::QRPAY_INST_MID;
        $data['requestTimestamp'] = now()->format('Y-m-d H:i:s');
    }

    /**
     * 异步分账确认
     * @param $datas
     */
    public function subOrdersConfirm($datas)
    {
        $uri = '/netpay/sub-orders-confirm';
        $data['instMid'] = self::QRPAY_INST_MID;
        $data['requestTimestamp'] = now()->format('Y-m-d H:i:s');
    }
}
