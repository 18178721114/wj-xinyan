<?php
namespace Air\Modules\Wechat;

use Air\Libs\Xcrypt;
use Air\Package\Fd16\CameraHandler;
use Air\Package\Sat\SatUser;

class Salesman_list extends \Air\Libs\Controller
{
    public $must_login = FALSE;

    public function run() {
        $request = $this->request->REQUEST;
        if (!$this->_init()) {
            return false;
        }
        $this->camera = CameraHandler::getCameraBySN($this->sn);
        $this->plain_sn = $this->camera['sn'];
        if (!$this->plain_sn) {
            $this->setView(1001, 'sn不存在！', '');
            return FALSE;
        }
        $this->user_id = $this->camera['user_id'];
        $users = SatUser::getLinesByRole(['sn' => $this->plain_sn, 'status' => 0, 'role' => [1,2]]);

        $detail = [];
        foreach ($users as $key => $value) {
            $detail[$key]['name'] = $value['name'];
            $detail[$key]['user_id'] = $value['user_id'];
            $detail[$key]['avatar'] = $value['avatar'];
            $detail[$key]['phone'] = Xcrypt::aes_decrypt($value['phone']);
        }
        
        $this->setView(0, 'success', $detail);
    }

    private function _init() {
        $request = $this->request->REQUEST;
        if (!$request['sn']) {
            $this->setView(1001, '缺少参数sn', '');
            return FALSE;
        }
        $this->sn = $request['sn'];
        return TRUE;
    }
}
