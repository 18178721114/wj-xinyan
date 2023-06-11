<?php

namespace Air\modules\pay;

use Air\Libs\ErrorCode;
use Air\Package\Pay\CheckOrder;
use Phplib\Tools\Logger;


/**
 * 微信支付后的回调函数 - 第三方定制版
 */
class Pay_notify_third_party extends \Air\Libs\Controller {
    public function run() {
        $request = $this->request;
        $order_id = $request->REQUEST['out_trade_no'];
        $status = $request->REQUEST['status'];
        $update_data = [
            'order_id'=> $order_id,
            'status' => $status,
        ];
        $order_info = CheckOrder::checkExist($order_id);
        Logger::info([
            "REQUES" => $request->REQUEST, "update_data" => $update_data, "order_info" => $order_info
        ], 'module-Pay_notify_third_party');
        if(empty($order_info)){
            $this->setView(ErrorCode::$OrderDoesNotExist, '未找到指定订单', '');
            return;
        }

        CheckOrder::updateInfo($update_data);
        $this->setView(0, '', '');
    }
}
