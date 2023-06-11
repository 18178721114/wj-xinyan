<?php
namespace Air\Modules\Pay;

use Air\Package\Checklist\CheckInfo;
use Air\Package\Wechat\WXUtil;
use Air\Libs\Xcrypt;
use Air\Package\Wechat\WechatUserCheck;
use Air\Package\Pay\WxPayApi;
use Air\Package\Pay\JsApiPay;
use Air\Package\Pay\WxPayUnifiedOrder;
use Air\Package\Pay\WxPayConfig;
use Air\Package\Pay\CheckOrder;
use Air\Package\User\Organizer;
use \Phplib\Tools\Logger;

class Config_init extends \Air\Libs\Controller
{
    private $use_pay = 1;
    private function init() {
        $this->url = \Air\Libs\Base\Utilities::decodeAmp($this->request->REQUEST['url']);
        Logger::error($this->url, 'config_init_url');
		if (empty($this->url)) {
            $this->setView(800001, 'url不能为空', []);
            return FALSE;
		}
		return TRUE;
    }

    public function run()
    {
		if (!$this->init()) {
			return FALSE;
		}
        $request = $this->request;
        if (ENV == 'production' || $this->use_pay) {
            Logger::error([ENV, $this->use_pay, WxPayConfig::APPID, WxPayConfig::APPSECRET], 'config_init_url');
            $wxobj = new WXUtil(WxPayConfig::APPID, WxPayConfig::APPSECRET);
        }
        else {
            $wxobj = new WXUtil(WX_APPID, WX_SECRET);
        }
        $jsapi_ticket = $wxobj->getTicket();
        $time = time();
        $nonceStr = md5($time);
        $data = [
            'jsapi_ticket' => $jsapi_ticket, 
            'noncestr' => $nonceStr, 
            'timestamp' => $time, 
            'url' => $this->url, 
        ];
        $sign = '';
        $items = [];
        foreach ($data as $key => $val) {
            $items[] = "{$key}={$val}";
        }
        $string = implode('&', $items);
        $return = [
            'appId' => (ENV == 'production' || $this->use_pay) ? WxPayConfig::APPID : WX_APPID,
            'timestamp' => $time, 
            'nonceStr' => $nonceStr, 
            'signature' => sha1($string), 
            'jsapi_ticket' => $jsapi_ticket, 
            'url' => $this->url, 
        ];
        Logger::error([$this->url, $data], 'config_init_url');
        $this->setView(0, '', $return);
    }
}
		//获取共享收货地址js函数参数
		//$editAddress = $tools->GetEditAddressParameters();
