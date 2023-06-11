<?php
/*************************************************************************
    > File Name: WxPay.class.php
    > Author: chenzihao
    > Created Time: Tue Jan  8 14:22:05 2019
  ************************************************************************/
namespace Air\Package\Yhpay;

use \Air\Libs\Base\Utilities;
use \Phplib\Tools\Logger;

class WxPay {

    private $mch_id = '1488378762';
    public $key = '5aa3719942f261b46f291ec16d8a1b98';
    private $api_cert = ROOT_PATH . '/package/yhpay/cert_airdoc/apiclient_cert.pem';
    private $api_key = ROOT_PATH . '/package/yhpay/cert_airdoc/apiclient_key.pem';
    private $appid = REGISTER_WX_APPID;
    private $openid;
    private $out_trade_no;
    private $body;
    private $total_fee;
    const UNIFY_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    const NOTIFY_URL = EYE_DOMAIN_HTTPS_PE . 'api/yhpay/receive';
    const REFUND_API = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    const REFUND_NOTIFY_API = EYE_DOMAIN_HTTPS_PE . 'api/yhpay/refund_receive';

    public function __construct($wechat_pay = [])
    {
        if ($wechat_pay) {
            $this->mch_id = $wechat_pay['appid'];
            $this->key = $wechat_pay['secret'];
            $key_path = $wechat_pay['template'][7]['template_content']['key_path'];
            $this->api_cert = ROOT_PATH . '/package/yhpay/' . $key_path . '/apiclient_cert.pem';
            $this->api_key = ROOT_PATH . '/package/yhpay/' . $key_path . '/apiclient_key.pem';
        }
    }

    public function getSign($parameters){
        ksort($parameters);
        $string = $this->toUrlParams($parameters);
        $string = $string . "&key=" . $this->key ;
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    }

    public function pay($body, $out_trade_no, $total_fee, $openid) {
        $this->body = $body;
        $this->out_trade_no = $out_trade_no;
        $this->total_fee = $total_fee;
        $this->openid = $openid;
        return $this->weiXinApp();
    }

    private function weiXinApp() {
        $unifiedorder = $this->unifiedorder();
        $parameters = [ 'appId' => $this->appid,
                        'timeStamp' => (string)time(),
                        'nonceStr' => Utilities::getUniqueId(),
                        'package' => 'prepay_id=' . $unifiedorder['prepay_id'],
                        'signType' => 'MD5',
                        ];
        $parameters['paySign'] = $this->getSign($parameters);
        return [$parameters, $unifiedorder];
    }

    private function unifiedorder() {
        $url = self::UNIFY_URL;
        $parameters = array (
            'appid' => $this->appid ,
            'mch_id' => $this->mch_id ,
            'nonce_str'  =>  Utilities::getUniqueId(),
            'body' => $this->body,
            'out_trade_no' => $this->out_trade_no ,
            'total_fee' => $this->total_fee,
            'spbill_create_ip' =>  Utilities::getClientIP(),
            'notify_url' =>  self::NOTIFY_URL,
            'openid' => $this->openid,
            'trade_type' => 'JSAPI'
        );
        $parameters['sign'] = $this->getSign($parameters);
        $xml_data = $this->arrayToXml($parameters);
        $result =$this->xmlToArray($this->postXmlCurl($xml_data, $url, 60));
        Logger::info(json_encode($parameters), 'Booking_parameters');
        $log_info = "[order_number] [" . $this->out_trade_no . "] [openid] [ ". $this->openid . "] [booking result] [" . $result['return_code'] . "] [detail] [" . json_encode($result) . "]";
        Logger::info($log_info, 'Booking_result_record');
        return $result;
    }

    private function postXmlCurl ($xml, $url, $second=30, $is_cert = 0){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT , $second);
        curl_setopt($ch, CURLOPT_URL , $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , FALSE);
        curl_setopt($ch, CURLOPT_HEADER , FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
        curl_setopt($ch, CURLOPT_POST , TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS , $xml);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 20);
        curl_setopt($ch, CURLOPT_TIMEOUT , 40);
        if ($is_cert) {
            curl_setopt($ch, CURLOPT_SSLCERT, $this->api_cert);
            curl_setopt($ch, CURLOPT_SSLKEY, $this->api_key);
        }
        set_time_limit(0);
        $data=curl_exec($ch);
        if ($data){
            curl_close($ch);
            return $data;
        } else {
            $info = curl_getinfo($ch);
            Logger::error($info, "curl_error_wxpay");
            return FALSE;
        }
    }
    //数组转换成xml
    private function arrayToXml($arr){
        $xml = "<xml>";
        foreach ($arr as $key => $val){
            if (is_array($val)){
                $xml .= "<" . $key . ">" . $this->arrayToXml($val) . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    //xml转换成数组
    private function xmlToArray($xml){
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA );
        $val = json_decode(json_encode($xmlstring), true);
        return $val;
    }

    public function toUrlParams($parameters){
        $string = '';
        if(!empty($parameters)){
            $array = [];
            foreach($parameters as $key => $value ){
                $array[] = $key.'='.$value;
            }
            $string = implode("&",$array);
        }
        return $string;
    }
}

