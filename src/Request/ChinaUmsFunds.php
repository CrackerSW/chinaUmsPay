<?php


namespace CrackerSw\ChinaUmsPay\Request;


use Illuminate\Support\Facades\Http;
use CrackerSw\ChinaUmsPay\Exceptions\InvalidArgumentException;

class ChinaUmsFunds
{
    /**
     * transCode： 202001
     *          param：merNo merOrderNo payAmt ps
     *
     * transCode: 202002
     *          param：merNo payAmt ps
     *
     * transCode: 202003
     *          param：merNo merOrderNo payAmt cardNo ps
     *
     * transCode: 202004
     *          param：merNo payType cardNo ps payAmt
     *
     * transCode: 202006
     *          param：merNo
     *
     * transCode: 202007
     *          param：merNo transDate queryItem queryValue
     *
     * transCode: 202008
     *          param：merNo reqDate reqJournalNo
     *
     * transCode: 202009
     *          param：merNo acctType payAmt acctName acctNo bankNo  bankName matchMode ps resume payerName payerAcctNo reserve1 reserve2
     *
     * transCode: 202010
     *          param：merNo orderType transDate reqJournalNo
     *
     * transCode: 202011
     *          param：merNo acctType
     *
     * transCode: 202012
     *          param：merNo orderType transDate beginAmt  endAmt  payerAcctNo payerName pageIdx
     *
     *  transCode: 202013
     *          param：merNo orderType acctNo acctName  payAmt  transDate reserve1 reserve2
     */
    const TRANSCODE_PAY_BY_JOURNAL = "202001";//按流水划付
    const TRANSCODE_PAY_BY_MONEY = "202002";//按金额划付
    const TRANSCODE_SPLIT_BY_JOURNAL = "202003";//按流水分账
    const TRANSCODE_SPLIT_BY_MONEY = "202004";//按金额分账
    const TRANSCODE_INFO_QUERY = "202006";//商户信息查询
    const TRANSCODE_DETAIL_QUERY = "202007";//交易明细查询
    const TRANSCODE_JOURNAL_QUERY = "202008";//操作记录查询
    const TRANSCODE_SINGLE_CURRENT_PAY = "202009";//单笔实时代付
    const TRANSCODE_SINGLE_QUERY = "202010";//订单单笔查询
    const TRANSCODE_BALANCE_QUERY = "202011";//商户余额查询
    const TRANSCODE_BATCH_QUERY = "202012";//订单批量查询
    const TRANSCODE_TRANSFER_CHECK = "202013";//转款校验

    public $error_message;

    protected $url;
    protected $debug = false;
    protected $tid;
    protected $mid;
    protected $group_id;
    protected $verNo = "100";
    protected $channelId = "043";
    protected $card_no_algo = "sha256";

    protected $private_key;
    protected $private_key_password;
    protected $public_key;

    protected static $now;

    public function __construct(array $config)
    {
        $this->debug = $config['debug'] ?? $this->debug;
        $this->tid = $config['tid'] ?? $this->tid;
        $this->mid = $config['mid'] ?? $this->mid;
        $this->group_id = $config['group_id'] ?? $this->group_id;
        $this->private_key = $config['private_key'] ?? $this->private_key;
        $this->private_key_password = $config['private_key_password'] ?? $this->private_key_password;
        $this->public_key = $config['public_key'] ?? $this->public_key;
        if ($this->debug) {
            $this->url = "https://mobl-test.chinaums.com/channel/Business/UnifyMulti/"; #测试地址
        } else {
            $this->url = "https://im.chinaums.com/channel/Business/UnifyMulti/"; #正式地址
        }
        self::$now = now('Asia/Shanghai');
    }

    /**
     * get error message
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }
    /*
     *
     *
     * 按流水
    字段	    第一组数据	第二组数据	第三组数据

    cardNo	6236681300001758484	6236681300001758484	6236681300001758484
    merOrderNo		AAAA0001	AAAA0002	AAAA0003


    按金额
    字段		第一组数据	第二组数据
    groupId	151515	151515
    merNo		10000000016327	10000000016327
    payAmt		1到1000任意值	1到1000任意值
    cardNo		6236681300001758484	6214832505283692



     * */
    /**
     * ex :
     * 202001 :transcodePayByJournal
     */
    public function transcodePayByJournal($data)
    {
        $data['payAmt'] = (string)$data['payAmt'];
        $header = $this->getHeader(self::TRANSCODE_PAY_BY_JOURNAL);
        $post_data = array_merge($header, $data);
        info([__METHOD__, __LINE__,$post_data]);
        return $this->sendRequest($post_data);
    }


    /**
     * ex :
     * 202002 :transcodePayByMoney
     */
    public function transcodePayByMoney($data)
    {
//        $data['groupId'] = $this->group_id;
        $header = $this->getHeader(self::TRANSCODE_PAY_BY_MONEY);
        $post_data = array_merge($header, $data);
        info([__METHOD__, __LINE__,$post_data]);
        return $this->sendRequest($post_data);
    }

    /**
     * ex :
     * 202006 :transcodeInfoQuery
     */
    public function transcodeInfoQuery($data)
    {
        $header = $this->getHeader(self::TRANSCODE_INFO_QUERY);
        $post_data = array_merge($header, $data);
//        info([__METHOD__, __LINE__,$post_data]);
        return $this->sendRequest($post_data);
    }

    /**
     * ex :
     * 202003 :query merno info
     */
    public function transcodeSplitByJournal($data)
    {
        $data['cardNo'] = hash($this->card_no_algo,$data['cardNo']);
        $header = $this->getHeader(self::TRANSCODE_SPLIT_BY_JOURNAL);
        $post_data = array_merge($header, $data);
//        info([__METHOD__, __LINE__,$post_data]);
        return $this->sendRequest($post_data);
    }

	/**
	 * ex :
	 * 202004 :query merno info
	 */
	public function transcodeSplitByMoney($data)
	{
		if (!isset($data['cardNo']) || !$data['cardNo']) {
			$this->error_message = '银行卡号不能为空';
			throw new InvalidArgumentException($this->error_message);
		}

		if (!isset($data['ps']) || !$data['ps']) {
			$this->error_message = '附言不能为空';
			throw new InvalidArgumentException($this->error_message);
		}

		$data['cardNo'] = hash($this->card_no_algo,$data['cardNo']);
		$data['payType'] = '0';
		$data['ps'] = urlencode($data['ps']);
		$header = $this->getHeader(self::TRANSCODE_SPLIT_BY_MONEY);
		$post_data = array_merge($header, $data);
//        info([__METHOD__, __LINE__,$post_data]);
		return $this->sendRequest($post_data);
	}

    public function transcodeDetailQuery($data)
    {
        $header = $this->getHeader(self::TRANSCODE_DETAIL_QUERY);
        $post_data = array_merge($header, $data);
//        info([__METHOD__, __LINE__,$post_data]);
        return $this->sendRequest($post_data);
    }

    //202008
    public function transcodeJournalQuery($data)
    {
//       merNo reqDate reqJournalNo
        $header = $this->getHeader(self::TRANSCODE_JOURNAL_QUERY);
        $post_data = array_merge($header, $data);
//        info([__METHOD__, __LINE__,$post_data]);
        return $this->sendRequest($post_data);
    }

    private function sendRequest($data)
    {
        $signature = $this->sign($data);
        $data['signature'] = $signature;
        $url = $this->url . $data['transCode'];
        info([__METHOD__, __LINE__, $url,$data]);
	    try {
		    $result = Http::withHeaders([
			    "Content-type" =>"application/json"
		    ])->post($url, $data)->throw();
	    } catch (\Exception $e) {
			info([__METHOD__, __LINE__,$e]);
			throw $e;
	    }
        return $result;
    }


    /**
     * group head json
     *
     * @param string transCode
     * @return array
     */
    protected function getHeader($transCode): array
    {
        return [
            'transCode' => $transCode,
            'verNo' => $this->verNo,
            'srcReqDate' => self::$now->format('Ymd'),
            'srcReqTime' => self::$now->format('His'),
            'srcReqId' => self::generateUniqueNumber(),
            'channelId' => $this->channelId,
            'groupId' => $this->group_id
        ];
    }


    protected static function generateUniqueNumber(): string
    {
        $micro = str_pad(substr(self::$now->micro, 0, 3), 3, '0', STR_PAD_LEFT);
        return self::$now->format('YmdHis') . $micro . self::randNum(10, 3);
    }

    /**
     * @param int $length
     * @param int $type 1数字，2大小写字母，3大小写字母数字
     * @return string
     */
    public static function randNum(int $length = 10, int $type = 1): string
    {
        switch ($type) {
            case 2:
                $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 3:
                $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                break;
            case 1:
            default:
                $str = '0123456789';
                break;
        }
        //字符组合
        $len = strlen($str) - 1;
        $randStr = '';
        for ($i = 0; $i < $length; $i++) {
            $num = mt_rand(0, $len);
            $randStr .= $str[$num];
        }
        return $randStr;
    }

    /**
     * group post json
     *
     * @param $params
     * @return string
     */
    public function getParamsString($params)
    {

        ksort($params);

        $paramsToBeSigned = [];
        foreach ($params as $k => $v) {
            if (is_array($params[$k])) {
                $v = json_encode($v, JSON_UNESCAPED_UNICODE);
            } else if (trim($v) == "") {
                continue;
            }
            if (is_bool($v)) {
                $paramsToBeSigned[] = $v ? "$k=true" : "$k=false";
            } else {
                $paramsToBeSigned[] = $k . '=' . $v;
            }
        }
        unset ($k, $v);

        $stringToBeSigned = (implode('&', $paramsToBeSigned));

        return $stringToBeSigned;
    }

    /**
     * sign function
     *
     * @param string $data
     *
     * @return string
     */
    public function sign($data)
    {
        $data = $this->getParamsString($data);
        $privateKey = $this->getPrivateKey();
//        info([__METHOD__,$privateKey]);
        if (openssl_sign(utf8_encode($data), $binarySignature, $privateKey, OPENSSL_ALGO_SHA256)) {
            return bin2hex($binarySignature);
        }

        $this->error_message = 'bin2hex not exit';
        throw new InvalidArgumentException($this->error_message);
    }

    /**
     * verifyRespondSign
     * @filePath cer fiie path
     * @param string $data　
     * @param string $signature
     *
     * @return bool
     */
    public function verifyRespondSign($data): bool
    {
        $signature = $data['signature'];
        unset($data['signature']);
        $string = $this->getParamsString($data);
        $pubKeyId = $this->getPublicKey();
        $signature = hex2bin($signature);
        $ok = openssl_verify($string, $signature, $pubKeyId, OPENSSL_ALGO_SHA256);

        if ($ok == 1) {
            openssl_free_key($pubKeyId);
            return true;
        }
        throw new InvalidArgumentException('验签失败');
    }

    private function getPrivateKey(): string
    {
        $filePath = $this->private_key;
        if (!file_exists($filePath)) {
            $this->error_message = 'private_key not exit';
            throw new InvalidArgumentException($this->error_message);
        }

        $pkcs12 = file_get_contents($filePath);
        openssl_pkcs12_read($pkcs12, $certs, $this->private_key_password);
        if (!$certs || !$certs['pkey']) {
            $this->error_message = 'certs not exit';
            throw new InvalidArgumentException($this->error_message);
        }

        return $certs['pkey'];
    }

    private function getPublicKey()
    {
        $filePath = $this->public_key;
        if (!file_exists($filePath)) {
            $this->error_message = '公钥文件不存在';
            throw new InvalidArgumentException($this->error_message);
        }

        $cert = file_get_contents($filePath);
        $cert = '-----BEGIN CERTIFICATE-----' . PHP_EOL
            . chunk_split(base64_encode($cert), 64, PHP_EOL)
            . '-----END CERTIFICATE-----' . PHP_EOL;
        $pubKeyId = openssl_get_publickey($cert);
        if (!$pubKeyId) {
            $this->error_message = '获取公钥失败';
            throw new InvalidArgumentException($this->error_message);
        }
        return $pubKeyId;
    }

    /**
     * 3Des加密
     *
     * @param $str
     * @return
     */
    private static function encrypt3Des($str)
    {
        $data = self::padding($str);
        $output = openssl_encrypt($data, 'DES-EDE3-CBC', hex2bin(self::config_3des_key), OPENSSL_NO_PADDING, pack('a8', ''));
        return bin2hex($output);

    }
}
