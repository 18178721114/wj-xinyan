<?php

namespace Air\Modules\Wechat;

use Air\Libs\Xcrypt;
use Air\Package\Cache\RedisCache;
use \Air\Package\Wechat\WXUtil;
use \Air\Package\User\PatientCode;
use \Air\Package\Fd16\CameraHandler;
use Air\Package\User\User;

/**
 * 第三方公众号获取Airdoc的H5登记页面。
 * STI第一个客户，部分是写死的，比如prefix
 */
class Get_register_url extends \Air\Libs\Controller
{
    public $must_login = TRUE;
    public function run()
    {
        if (!$this->_init()) {
            return false;
        }
        $request = $this->request->REQUEST;
        $qr_params = ['en_open_id' => Xcrypt::encrypt($request['openid'])];
        $camera = CameraHandler::getCameraBySN(md5($request['sn']));
        $plain_sn = $camera['sn'];
        $user_id = $camera['user_id'];
        if (!$user_id) {
            $this->setView(10004, '设备未绑定账号！', []);
            return FALSE;
        }
        $user_obj = new User();
        $user = $user_obj->getUserById($user_id);
        if ($user['org_id'] != $this->userSession['org_id']) {
            $this->setView(10005, '该设备绑定的账号，您没有权限！', []);
            return FALSE;
        }
        $prefix = '8991';
        if ($user['org']['customer_id'] == 5) {
            $prefix = '8997';
        } elseif (in_array($user['org']['customer_id'], [15, 17])) {
            $prefix = '8996';
        }
        $code = PatientCode::getFreeCodeByOpenid($request['openid'], $prefix);
        if (!$code) {
            list($id, $code) = PatientCode::initCode($request['openid'], $prefix);
        }
        $qr_params['is_fd16'] = 1;
        unset($request['sn']);
        $qr_params['sn'] = $camera['md5'];
        $qr_params['pcode'] = $code;
        foreach (WXUtil::QR_PARAMS as $column) {
            if (!empty($request[$column])) {
                $qr_params[$column] = $request[$column];
            }
        }
        $query_string = http_build_query($qr_params);
        $url = EYE_DOMAIN_HTTPS_PE . "fd16/fulluserinfo/set?{$query_string}";
        $show_fd16_video_str = intval($user['org']['config']['show_fd16_video']) === 1 ? '&show_fd16_video=1' : '';
        $show_fd16_qrcode_str = intval($user['org']['config']['show_fd16_qrcode']) === 1 || $camera['work_mode'] == 4 ? '&show_fd16_qrcode=1' : '';
        $work_mode_str = '&work_mode=' . $camera['work_mode'];
        $age_type = $user['org']['age_type'];
        $age_type_str = "&age_type={$age_type}";
        $work_mode_str .= (in_array($user['org']['customer_id'], ZY_CUSTOMER_IDS) ? '&is_zhongyou=1' : '');
        $register_type = ($user['config']['register_type'] == -1 || !isset($user['config']['register_type'])) ? $user['org']['config']['register_type'] : $user['config']['register_type'];
        $camera['work_mode'] == 4 && $register_type = 1;
        $register_type_str = '&register_type=' . $register_type;
        $substr6Sn = '&substr6Sn=' . substr($camera['sn'], -6);
        $url .= $show_fd16_video_str . $show_fd16_qrcode_str . $work_mode_str . $register_type_str . $substr6Sn . $age_type_str;
        $data = ['url' => $url, 'expire_in' => date('Y-m-d H:i:s', time() + 3600)];
        if ($user['org']['config']['rigister_miniprogram']) {
            $data['url'] = WXUtil::h5Url2Miniprogram($url);
            $data['mini_appid'] = REGISTER_WX_APPID;
            // BAEQ-3210 蓝颐
            if ($request['medical_record_no']) {
                RedisCache::setCache('pcode_2_medical_record_no_' . $code, trim($request['medical_record_no']), '', 86400);
            }
        }
        $this->setView(0, '', $data);
    }
    private function _init()
    {
        $request = $this->request->REQUEST;
        if (empty(trim($request['openid']))) {
            $this->setView(10003, '请提供openid', []);
            return FALSE;
        }
        if (empty(trim($request['sn']))) {
            $this->setView(10003, '请提供sn', []);
            return FALSE;
        }
        return TRUE;
    }
}
