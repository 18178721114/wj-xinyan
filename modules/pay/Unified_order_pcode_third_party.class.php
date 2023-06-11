<?php
/**
 * 支持三方支付的统一下单，所以三方支付需要在神农系统注册， 并关联"Airdoc筛查登记"的小程序
 */
namespace Air\modules\pay;

use Air\Package\Pay\CheckOrder;
use Air\Package\Smb\SnPcode;
use Air\package\thirdparty\sdk\Shennong\ShenNong;
use Air\Package\User\Organizer;
use Air\Package\User\PatientCode;
use Air\Package\Wechat\WechatMiniProgram;
use Phplib\Tools\Logger;

class Unified_order_pcode_third_party extends \Air\Libs\Controller {
    /**
     * @var int
     */
    private $pcode;
    /**
     * @var float|int
     */
    /**
     * @var string
     */
    private $code;

    private function init() {
        $request = $this->request;
        $this->code = $request->REQUEST['code'];
        $this->pcode = (int)$request->REQUEST['pcode'];
        return TRUE;
    }

    public function run() {
        if (!$this->init()) {
            return FALSE;
        }
        $old = $this->old;
        # 获取小程序的openid
        $auth = WechatMiniProgram::getAuth($this->code);
        if (isset($auth['errcode'])) {
            $this->setView(1, '', $auth['errmsg']);
            return false;
        }
        $openId = $auth['openid'];
        $order_infos = CheckOrder::checkExistByPcode($this->pcode);
        $patient_code = PatientCode::getItemByPcode($this->pcode);
        Logger::error([
            "order_infos"=>$order_infos, "patient_code"=>$patient_code, "openId"=>$openId,
            "pcode"=>$this->pcode,
        ], 'unified_order_pcode_third_party');
        $order_id = null;
        $order_info = null;
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
        ];
        $org_obj = new Organizer();
        $org_info = $org_obj->getOrganizerById($old['org_id']);
        !$order_info && $order_info = CheckOrder::checkExist($order_id);
        Logger::error( ["CheckOrder data"=>$data, "order_info"=>$order_info], 'unified_order_pcode_third_party');
        if (!$order_info) {
            CheckOrder::create($data);
        }
        Logger::error([
            "order_id" =>$order_id, "order_info"=>$order_info
        ], 'unified_order_pcode_third_party');
        if ($openId && $order_info && $order_info['prepay_info'] && time() - strtotime($order_info['prepay_time']) < 1800) {
            $jsApiParameters = unserialize($order_info['prepay_info']);
            $this->setView(0, '', $jsApiParameters);
            return TRUE;
        }
        $pcode2sn = SnPcode::getSnPcode(['pcode'=>$this->pcode]);
        if(empty($pcode2sn)){
            $this->setView(800014, "获取设备信息失败", $this->pcode);
            return FALSE;
        }
        $pcode2sn = $pcode2sn[0];
        $shen_nong_client = new ShenNong(SHEN_NONG_DOMAIN);
        // 调用统一下单
        $order = $shen_nong_client->unifiedorder(
            $patient_code['org_id'], $pcode2sn['sn'], $pcode2sn['user_id'],
            "健康评估", $order_id,
            $openId, "", $org_info['name'],
            date("YmdHis"),
            date("YmdHis", time() + 3600),
            "健康评估",
            EYE_DOMAIN_HTTPS_PE . "api/pay/pay_notify"
        );
        Logger::error(['unifiedorder res'=>$order], 'Unified_order_pcode_third_party');
        if (!$order['result_code'] || $order['result_code'] != 'SUCCESS') {
            Logger::error([$order, $openId], 'Unified_order_pcode_third_party');
            $this->setView(800013, '统一下单API出错', $order);
            return false;
        }
        $update_data = [
            'order_id' => $order_id,
            'status' => 0,
            'prepay_id' => $order['prepay_id'],
            'prepay_time' => date('Y-m-d H:i:s'),
            'prepay_info' => serialize($order),
        ];
        CheckOrder::updateInfo($update_data);
        $this->setView(0, '', $order);
    }
}
