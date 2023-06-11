<?php

namespace Air\Modules\Pay;

use Air\Package\Checklist\CheckInfo;
use Air\Package\Wechat\WXUtil;
use Air\Libs\Xcrypt;
use Air\Package\Wechat\WechatUserCheck;
use Air\Package\Pay\WxPayApi;
use Air\Package\Pay\JsApiPay;
use Air\Package\Pay\WxPayUnifiedOrder;
use Air\Package\Pay\CheckOrder;
use Air\Package\Pay\Helper\RedisPay;
use Air\Package\User\Organizer;
use \Phplib\Tools\Logger;

class Unified_order extends \Air\Libs\Controller
{
    private function init()
    {
        $request = $this->request;
        $this->check_id = (int) $request->REQUEST['check_id'];
        $this->to_package_type = $request->REQUEST['to_package_type'] ? intval($request->REQUEST['to_package_type']) : 2;
        if (!in_array($this->to_package_type, [1, 2, 3, 4])) {
            $this->setView(800001, '升级的套餐不符合规范', []);
            return FALSE;
        }
        $this->en_check_id = (string) $request->REQUEST['en_check_id'];
        if (empty($this->check_id) && $this->en_check_id) {
            $this->check_id = \Air\Libs\Xcrypt::decrypt($this->en_check_id);
        }
        if (!$this->check_id) {
            $this->setView(800001, '检查ID不符合规范', []);
            return FALSE;
        }
        $obj = new CheckInfo();
        $result = $obj->getCheckInfoSelfById($this->check_id);
        if (!$result) {
            $this->setView(800002, '检查ID不符合规范', []);
            return FALSE;
        }
        $this->old = $old = $result[0];
        $this->to_package_type = $this->old['recommend'];
        if ($this->to_package_type <= $old['package_type']) {
            $this->setView(800003, '已经升级过套餐，请不要重复升级。', []);
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
        $old = $this->old;
        //①、获取用户openid
        $cache_key = md5($_COOKIE[SESSION_ID]);
        $openId = RedisPay::getCache($cache_key);
        $tools = new JsApiPay();
        if (!$openId) {
            $openId = $tools->GetOpenid();
            RedisPay::setCache($cache_key, $openId);
        }
        $order_infos = CheckOrder::checkExistByCheckId($this->check_id);
        if ($order_infos) {
            foreach ($order_infos as $order_item) {
                if ($order_item['open_id'] == $openId) {
                    $order_id = $order_item['order_id'];
                    $order_info = $order_item;
                    break;
                }
            }
        }
        if (!$order_id) {
            $order_id = date('ymd') . $this->check_id . count($order_infos) . $old['package_type'] . $this->to_package_type;
        }
        $data = [
            'order_id' => $order_id,
            'check_id' => $this->check_id,
            'status' => -1,
            'org_id' => $old['org_id'],
            'patient_id' => $old['patient_id'],
            'from_package_type' => $old['package_type'],
            'to_package_type' => $this->to_package_type,
            'open_id' => $openId,
        ];
        $org_obj = new Organizer();
        $org_info = $org_obj->getOrganizerById($old['org_id']);
        !$order_info && $order_info = CheckOrder::checkExist($order_id);
        if (!$order_info) {
            CheckOrder::create($data);
        }
        Logger::error("order_id: " . $order_id, 'pay_debug');
        if ($openId && $order_info && time() - strtotime($order_info['prepay_time']) < 1800) {
            $jsApiParameters = $tools->GetJsApiParameters(unserialize($order_info['prepay_info']));
            $this->setView(0, '', $jsApiParameters);
            return TRUE;
        }
        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("基础套餐升级");
        $input->SetAttach($org_info['name']);
        $input->SetOut_trade_no($order_id);
        //TODO 1分
        if ($this->to_package_type == 2) {
            $input->SetTotal_fee(UPGRADE_C_FEE);
        } else {
            $input->SetTotal_fee(UPGRADE_B_FEE);
        }
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 1800));
        $input->SetGoods_tag("视网膜检查套餐升级");
        $input->SetNotify_url(EYE_DOMAIN . "api/pay/pay_notify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        if (!$order['result_code'] || $order['result_code'] != 'SUCCESS') {
            Logger::error([$order, $openId], 'unified_order_error');
            $this->setView(800013, '统一下单API出错', []);
            return;
        }
        $update_data = [
            'order_id' => $order_id,
            'status' => 0,
            'prepay_id' => $order['prepay_id'],
            'prepay_time' => date('Y-m-d H:i:s'),
            'prepay_info' => serialize($order),
        ];
        CheckOrder::updateInfo($update_data);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $this->setView(0, '', $jsApiParameters);
    }
}
		//获取共享收货地址js函数参数
		//$editAddress = $tools->GetEditAddressParameters();
