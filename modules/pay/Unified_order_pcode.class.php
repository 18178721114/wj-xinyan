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
use Air\Package\User\PatientCode;
use \Phplib\Tools\Logger;

class Unified_order_pcode extends \Air\Libs\Controller
{
    private function init()
    {
        $request = $this->request;
        $this->pcode = (int) $request->REQUEST['pcode'];
        $this->pay_price = (float) $request->REQUEST['pay_price'];
        return TRUE;
    }

    public function run()
    {
        if (!$this->init()) {
            return FALSE;
        }
        $request = $this->request;
        $old = $this->old;
        $patient_code = PatientCode::getItemByPcode($this->pcode);
        $openId = $patient_code['openid'];
        if (!$openId) {
            $this->setView(1, 'openid 出错了', '');
            return TRUE;
        }
        $tools = new JsApiPay();
        // if (!$openId && $this->request->REQUEST['en_open_id']) {
        //     $openId = Xcrypt::decrypt(trim($this->request->REQUEST['en_open_id']));
        // }
        // if (!$openId) {
        //     $openId = $tools->GetOpenid();
        //     RedisPay::setCache($cache_key, $openId);
        // }
        $order_infos = CheckOrder::checkExistByPcode($this->pcode);
        $patient_code = PatientCode::getItemByPcode($this->pcode);
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
            $order_id = $this->pcode . count($order_infos);
        }
        $data = [
            'order_id' => $order_id,
            'pcode' => $this->pcode,
            'status' => -1,
            'org_id' => $patient_code['org_id'],
            'patient_id' => $patient_code['patient_id'],
            'from_package_type' => 0,
            'to_package_type' => 0,
            'open_id' => $openId,
            'price' => intval($this->pay_price * 100)
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
        $input->SetBody("健康评估");
        $input->SetAttach($org_info['name']);
        $input->SetOut_trade_no($order_id);
        //TODO 1分
        $this->pay_price = intval($this->pay_price * 100);
        $input->SetTotal_fee($this->pay_price);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 1800));
        $input->SetGoods_tag("健康评估");
        $input->SetNotify_url(EYE_DOMAIN_HTTPS_PE . "api/pay/pay_notify");
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
