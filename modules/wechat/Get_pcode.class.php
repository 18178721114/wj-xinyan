<?php

namespace Air\Modules\Wechat;

use Air\Libs\Base\Utilities;
use Air\Package\Fd16\CameraHandler;
use Air\Package\Thirdparty\ThirdHandler;
use \Air\Package\User\PatientCode;
use Air\Package\User\User;

/**
 * 只适用鹰瞳健康，open api 
 */
abstract class Get_pcode extends \Air\Libs\Controller
{
    protected $runtime = 1;
    public function run()
    {
        $request = $this->request->REQUEST;
        $openid = trim($request['openid']);

        $sn = trim($request['sn']);
        if (empty($openid)) {
            $this->setView(11, '缺少openid参数', '');
            return FALSE;
        }
        if (empty($sn)) {
            $this->setView(12, '缺少sn参数', '');
            return FALSE;
        }
        $camera = CameraHandler::getCameraOriginSN($sn)[0];
        if (!$camera) {
            Utilities::DDMonitor("P3-pangu-【太平人寿-李瑞】系统中未找到此设备, SN为：{$sn}", 'cloudm', TRUE);
            $this->setView(13, 'sn 不存在', '');
            return FALSE;
        }
        $user_id = $this->camera['user_id'];
        $user_obj = new User();
        $this->fd16_user = $user_obj->getUserById($user_id);
        if (in_array($this->fd16_user['org_id'], ThirdHandler::ORG_IDS['taiping'])) {
            $this->setView(14, '绑定的账号机构ID不符合规范。', []);
            Utilities::DDMonitor("P3-pangu-请先绑定设备到【太平人寿-李瑞】机构下的账号, SN为：{$sn}", 'cloudm', TRUE);
            return FALSE;
        }
        $prefix = ZY_PCODE_PREFIX;
        if ($this->fd16_user['org']['customer_id'] == 5) {
            $prefix = ICVD_PCODE_PREFIX;
        }
        $type = 0;
        $not_push = 1;
        $new_wechat = 2;
        list($id, $pcode) = PatientCode::initCode($openid, $prefix, $not_push, $type, $new_wechat);
        $this->setView(0, '', ['pcode' => $pcode, 'expire_in' => date('Y-m-d H:i:s')]);
        return TRUE;
    }
}
