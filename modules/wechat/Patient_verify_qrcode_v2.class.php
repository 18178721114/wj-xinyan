<?php

namespace Air\Modules\Wechat;

use Air\Libs\Base\Utilities;
use \Air\Libs\Xcrypt;
use \Air\Package\Fd16\CameraHandler;
use \Air\Package\User\PatientCode;
use \Air\Package\User\User;
use Air\Package\Thirdparty\ThirdHandler;

class Patient_verify_qrcode_v2 extends \Air\Libs\Controller
{
    public $must_login = false;
    public $general = 0;
    public $runtime = 1;
    private $fd16_user = [];

    public function run()
    {
        if (!$this->_init()) {
            return false;
        }
        $camera = $this->camera;
        $user = $this->fd16_user;
        $register_type = ($user['config']['register_type'] == -1 || !isset($user['config']['register_type'])) ? $user['org']['config']['register_type'] : $user['config']['register_type'];
        $camera['work_mode'] == 4 && $register_type = 1; // 先登记
        $show_pay_page = $user['show_pay_page'] == -1 ? $user['org']['config']['show_pay_page'] : $user['show_pay_page'];
        $vcode = 0;
        $jump_authorize = 0;
        if ($show_pay_page == 2 || in_array($user['org_id'], VCODE_ORG_ID)) {
            $vcode = 1;
            $jump_authorize = 1;
        }
        $substr6Sn = substr($camera['sn'], -6);
        $model = isset($camera['model']) ? $camera['model'] : '';

        $org = $user['org'];
        $price = SALESMAN_PRICE_DEFAULT;
        $origin_price = SALESMAN_ORIGIN_PRICE_DEFAULT;
        if ($org) {
            if (isset($org['config']['salesman_price'])) {
                $salesman_price = intval($org['config']['salesman_price']);
                $price = $salesman_price ? $salesman_price : $price;
            }
            if (isset($org['config']['salesman_origin_price'])) {
                $salesman_origin_price = intval($org['config']['salesman_origin_price']);
                $origin_price = $salesman_origin_price ? $salesman_origin_price : $origin_price;
            }
        }
        if (in_array($user['org_id'], TAIKANG_ORG_ID)) {
            $price = 598;
        }
        $data = [
            'sn' => $camera['md5'],
            // 'openid' => $this->openid, 
            'pcode' => $this->pcode,
            't' => time(),
            'price' => $price,
            'origin_price' => $origin_price,
            'vcode' => $vcode,
            'hide_vcode_front' => ($this->fd16_user['is_taikang_yintong'] || $this->fd16_user['is_taikang_zhongyou'] || $this->fd16_user['is_taikang_yt_health']) ? 1 : 0,
            'substr6Sn' => $substr6Sn,
            'jump_authorize' => $jump_authorize,
            'age_type' => $this->fd16_user['org']['age_type'],
            'is_yintong' => $this->fd16_user['is_yintong'],
            'is_zhongyou' => $this->fd16_user['is_zhongyou'],
            'register_type' => $register_type,
            'org_id' => $this->fd16_user['org_id'],
            'check_agent_num' => $user['check_agent_num'],
            'show_fd16_video' => $user['org']['config']['show_fd16_video'],
            'show_fd16_qrcode' => $user['org']['config']['show_fd16_qrcode'],
            'model' => $model,
        ];
        $this->setView(0, '验证成功', $data);
        return false;
    }

    private function _init()
    {
        $request = $this->request->REQUEST;
        $sn_from_key = Xcrypt::decryptAes(trim($request['key']), ThirdHandler::AES_KEY['common']);
        $this->sn = trim($request['sn']); //明码sn
        if (empty($this->sn)) {
            $this->setView(10005, '健康扫描仪的序列号不能为空', []);
            return false;
        }
        if ($sn_from_key != $this->sn) {
            $this->setView(10001, '二维码验证失败', []);
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
        $user_obj = new User();
        $this->fd16_user = $user_obj->getUserById($user_id);
        $this->fd16_user['is_taikang_yintong'] = (in_array($this->fd16_user['org_id'], TAIKANG_ORG_ID) && $this->fd16_user['org']['customer_id'] == 5) ? 1 : 0;
        $this->fd16_user['is_taikang_zhongyou'] = in_array($this->fd16_user['org_id'], TAIKANG_ZY_ORG_ID) ? 1 : 0;
        $this->fd16_user['is_taikang_yt_health'] = in_array($this->fd16_user['org_id'], TAIKANG_YTH_ORG_ID) ? 1 : 0;
        $this->fd16_user['is_zhongyou'] = 0;
        $this->fd16_user['is_yintong'] = 0;
        if ($this->fd16_user['is_taikang_yintong']) {
            $prefix = ICVD_PCODE_PREFIX;
            $this->fd16_user['is_yintong'] = 1;
        } elseif ($this->fd16_user['is_taikang_zhongyou']) {
            $prefix = ZY_PCODE_PREFIX;
            $this->fd16_user['is_zhongyou'] = 1;
        } elseif ($this->fd16_user['is_taikang_yt_health']) {
            $prefix = ZY_PCODE_PREFIX;
            $this->fd16_user['is_yintong'] = 1;
        } elseif ($this->fd16_user['org']['customer_id'] == 5 && $this->fd16_user['org']['config']['show_pay_page'] == 2) {
            $prefix = ICVD_PCODE_PREFIX;
            $this->fd16_user['is_yintong'] = 1;
        } else {
            $prefix = ZY_PCODE_PREFIX;
            $this->fd16_user['is_yintong'] = 1;
        }
        $this->openid = 'QRCode_V2_' . Utilities::getUniqueId();
        list($id, $this->pcode) = PatientCode::initCode($this->openid, $prefix, 1, 4, 0);
        if (!$this->pcode) {
            $this->setView(10004, '请提供pcode', []);
            return false;
        }
        return true;
    }
}
