<?php
namespace Air\Modules\Wechat;

use Air\Package\Fd16\CameraHandler;
use Air\Package\User\Helper\DBCustomerHelper;
use Air\Package\User\Organizer;
use Air\Package\User\User;

/**
 * 是否是夜间模式，慧心瞳筛查小程序提醒
 */
class CheckNight extends \Air\Libs\Controller
{
    protected $sn;

    public function run()
    {
        if(!$this->_init()) {
            return false;
        }
        // $this->sn
        $camera_data = CameraHandler::getCameraSnByData($this->sn);
        if (!empty($camera_data)) {
            $user_id = isset($camera_data['user_id']) ? $camera_data['user_id'] : 0;
            $user = new User();
            $user_data = $user->getUserById($user_id);
            $org_id = isset($user_data['org_id']) ? $user_data['org_id'] : 0;
            if ($org_id) {
                $organizer = new Organizer();
                $org_info = $organizer->getOrganizerById($org_id);
                $customer_id = isset($org_info['customer_id']) ? $org_info['customer_id'] : 0;
                $customer_info = DBCustomerHelper::getLines(['id' => $customer_id]);
                \Phplib\Tools\Logger::info([$this->sn, $customer_info[0]['type']], 'click_push_debug');
                if(isset($customer_info[0]['type']) && $customer_info[0]['type'] == 'huixintong') {
                    // 慧心瞳夜间模式开始时间与结束时间
                    if (date('H') >= '23' || date('H') < '07') {
                        $this->setView(0, '23:00～7:00为系统维护时间，您的报告将顺延至7:00后生成', '');
                        return false;
                    }
                }
            }
        }
        $this->setView(10040, 'success', '');
        return false;
    }

    private function _init()
    {
        $request = $this->request->REQUEST;
        if (empty($request['sn'])) {
            $this->setView(10001, '缺少设备编码', '');
            return false;
        }
        $md5sn = trim($request['sn']);
        if (empty($md5sn)) {
            $this->setView(10056, '缺少参数', '');
            return false;
        }
        $camera_data = CameraHandler::getCameraBySN($md5sn);
        $this->sn = isset($camera_data['sn']) ? $camera_data['sn'] : "";
        if (empty($this->sn)) {
            $this->setView(10062, '该设备不存在', '');
            return false;
        }
        return true;
    }
}
