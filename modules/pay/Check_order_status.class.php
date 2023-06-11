<?php
/**
 * 支持三方支付的统一下单，所以三方支付需要在神农系统注册， 并关联"Airdoc筛查登记"的小程序
 */
namespace Air\modules\pay;

use Air\Package\Pay\CheckOrder;

class Check_order_status extends \Air\Libs\Controller {
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
        $check_order = CheckOrder::checkExistByPcode($this->pcode);
        if(count($check_order) >1 || count($check_order)==0){
            $this->setView(10011, gettext('筛查码无效，请重新扫码获取。'), []);
            return false;
        }
        $check_order = $check_order[0];
        $this->setView(0, '', ['status'=>$check_order['status']]);
        return TRUE;
    }
}
