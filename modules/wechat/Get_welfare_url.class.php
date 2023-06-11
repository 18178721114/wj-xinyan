<?php

namespace Air\Modules\Wechat;

use Air\Libs\Base\Utilities;
use \Air\Libs\Xcrypt;
use \Air\Package\Fd16\CameraHandler;
use \Air\Package\User\PatientCode;
use \Air\Package\User\User;
use Air\Package\Thirdparty\ThirdHandler;

class Get_welfare_url extends \Air\Libs\Controller
{
    public $must_login = TRUE;
    public function run()
    {
        if (!$this->_init()) {
            return false;
        }
        $key = Xcrypt::encryptAes($this->sn, ThirdHandler::AES_KEY['common']);
        $url = EYE_DOMAIN_HTTPS_PE . "icvd/welfare?sn={$this->sn}&key=$key";
        $this->setView(0, '获取成功', ['url' => $url]);
        return true;
    }

    private function _init()
    {
        $request = $this->request->REQUEST;

        $this->sn = trim($request['sn']); //明码sn
        if (empty($this->sn)) {
            $this->setView(10005, '健康扫描仪的序列号不能为空', []);
            return false;
        }
        $this->camera = CameraHandler::getCameraOriginSN($this->sn)[0];
        if (!$this->camera) {
            $this->setView(10002, '设备SN不存在', []);
            return false;
        }
        $user_id = $this->camera['user_id'];
        if (empty($user_id)) {
            $this->setView(10004, '设备SN未绑定账号', []);
            return false;
        }
        if ($this->camera['org_id'] != $this->userSession['org_id']) {
            $this->setView(10004, '上传机构不一致', []);
            return false;
        }


        return true;
    }
}
