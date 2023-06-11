<?php
namespace Air\Modules\Pay;
use Air\Package\Checklist\CheckInfo;
use Air\Package\Pay\CheckOrder;
use Air\Package\Wechat\WXUtil;
use Air\Libs\Xcrypt;
use Air\Package\Wechat\WechatUserCheck;
use Air\Package\Pay\WxPayApi;
use Air\Package\Pay\PayNotifyCallBack;
use Air\Package\Pay\JsApiPay;

class Pay_notify extends \Air\Libs\Controller
{
    public function run()
    {
		$notify = new PayNotifyCallBack();
		$notify->Handle(false);
        exit;
    }
}
