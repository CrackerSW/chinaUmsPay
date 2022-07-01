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
     */
    public function getQrcode($data): array
    {
        $uri = '/netpay/bills/get-qrcode';
        $data['billNo'] = $this->createMerOrderId();
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
    }

    /**
     * 关闭二维码
     */
    public function closeQrcode()
    {
        $uri = '/netpay/bills/close-qrcode';
    }

    /**
     * 账单查询
     * @param $data
     */
    public function billsQuery($data)
    {
        $uri = '/netpay/bills/query';
    }

    /**
     * 退款
     */
    public function billsRefund()
    {
        $uri = '/netpay/bills/refund' ;
    }

    /**
     * 二维码信息查询
     * @param $data
     */
    public function queryQrcodeInfo($data)
    {
        $uri = '/netpay/bills/query-qrcode-info' ;
    }

    /**
     * 异步分账确认
     * @param $datas
     */
    public function subOrdersConfirm($datas)
    {
        $uri = '/netpay/sub-orders-confirm';
    }


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
