<?php

namespace Air\Modules\Wechat;

use \Air\Libs\Xcrypt;
use Air\Package\Patient\Patient;
use Air\Package\Pay\CheckOrder;
use \Air\Package\User\PatientCode;
use \Air\Package\Wechat\WXUtil;
use \Air\Package\Wechat\Wechat;
use Air\Package\Wechat\WechatThird;
use \Phplib\Tools\Logger;

class Jump extends \Air\Libs\Controller
{
    public function run()
    {
        $request = $this->request->REQUEST;
        Logger::error($request, 'wechat_jump');
        $openid = Xcrypt::decrypt($request['en_open_id']);
        $wx_util = intval($request['is_new'])
            ? new WXUtil(WX_APPID_NEW, WX_SECRET_NEW)
            : new WXUtil(WX_APPID, WX_SECRET);
        if (strpos($openid, ZY_WX_OPENID_PREFIX) === 0) {
            Logger::error($openid . ' - ' . ZY_WX_APPID . 'debug', 'wechat_jump');
            $wx_util = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
        }
        if (strpos($openid, TZJ_WX_OPENID_PREFIX) === 0) {
            Logger::error($openid . ' - ' . TZJ_WX_APPID . 'debug', 'wechat_jump');
            $wx_util = new WXUtil(TZJ_WX_APPID, TZJ_WX_SECRET);
        }
        if (strpos($openid, YTHEALTH_WX_OPENID_PREFIX) === 0) {
            Logger::error($openid . ' - ' . YTHEALTH_WX_APPID . 'debug', 'wechat_jump');
            $wx_util = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);
        }
        $sub_openid = substr($openid, 0, 5);
        $wechat_config_data['prefix'] =  $sub_openid;
        $wechat_config = WechatThird::getWechatConfig($wechat_config_data);
        if ($wechat_config) {
            $wx_util = new WXUtil($wechat_config['appid'], $wechat_config['secret']);
        }
        $code = $request['code'];
        $register_type = 0;
        if ($request['register_type'] || !isset($request['register_type'])) {
            $register_type = 1;
        }
        $result = $wx_util->getAuthAccessToken($code);
        $info = Wechat::getRecordByOpenid($result['openid']);
        if (empty($info) && $result['openid']) {
            $openid = $result['openid'];
            $user_info = $wx_util->getUserInfo($result['access_token'], $result['openid']);
            if ($user_info['nickname']) {
                $id = Wechat::addRecord([
                    'openid' => $openid,
                    'nickname' => $user_info['nickname'],
                    'sex' => $user_info['sex'],
                    'city' => $user_info['city'],
                    'province' => $user_info['province'],
                    'country' => $user_info['country'],
                    'headimgurl' => $user_info['headimgurl'],
                    'wechat_type' => (int) $request['is_new'],
                ]);
            } else {
                Logger::error($user_info, 'wechat_jump');
            }
        }
        Logger::error($code, 'wechat_jump');
        Logger::error($result, 'wechat_jump');
        $qr_params = ['en_open_id' => Xcrypt::encrypt($openid)];
        foreach (WXUtil::QR_PARAMS as $column) {
            if (!empty($request[$column])) {
                $qr_params[$column] = $request[$column];
            }
        }
        $query_string = http_build_query($qr_params);
        $hospital_search = trim($request['frm']);
        $is_fd16 = trim($request['is_fd16']);
        $org_id = trim($request['org_id']);
        $pcode = trim($request['pcode']);
        if ($hospital_search == 'hospital_search') {
            $destUrl = EYE_DOMAIN_HTTPS_PE . "user/uSrhRpt?{$query_string}";
        }
        else if ($is_fd16) {
            $pcode_item = PatientCode::getItemByPcode($pcode);
            if ($pcode_item['patient_id']) {
                $destUrl = EYE_DOMAIN_HTTPS_PE . "fd16/start?{$query_string}";
                $patient = Patient::getPatientsByIds($pcode_item['patient_id'])[$pcode_item['patient_id']];
                if ($patient) {
                    $destUrl .= '&name=' . $patient['name'];
                }
            } elseif ($request['a']) {
                $destUrl = EYE_DOMAIN_HTTPS_PE . "fd16/fulluserinfo/seta?{$query_string}";
            } elseif (!empty($qr_params['pay_price'])) {
                $destUrl = EYE_DOMAIN_HTTPS_PE . "wx/payment?{$query_string}";
                $order = CheckOrder::checkExistByPcode($pcode);
                if ($order) {
                    $order = $order[0];
                    if ($order['status'] == 10 || $order['status'] == 20) {
                        $destUrl = EYE_DOMAIN_HTTPS_PE . "fd16/fulluserinfo/set?{$query_string}";
                    }
                }
            } elseif (!$register_type) {
                $destUrl = EYE_DOMAIN_HTTPS_PE . "fd16/userinfo/set?{$query_string}";
            } elseif (!empty($qr_params['tibet'])) {
                $destUrl = EYE_DOMAIN_HTTPS_PE . "user/xzregister?{$query_string}";
            } else {
                $destUrl = EYE_DOMAIN_HTTPS_PE . "fd16/fulluserinfo/set?{$query_string}";
            }
        }
        else if (in_array($org_id, [40104, 40143])) {
            $destUrl = EYE_DOMAIN_HTTPS_PE . "hospital/userinfo/search?{$query_string}";
        } else if (0 && !empty($org_id) && empty($request['is_ak_outside'])) { // 3324 有机构id都进入大相机
            $destUrl = EYE_DOMAIN_HTTPS_PE . "hospital/userinfo/search?{$query_string}";
        } else if (isset($qr_params['tibet'])) {
            $destUrl = EYE_DOMAIN_HTTPS_PE . "user/xzregister?{$query_string}";
        } else if (isset($qr_params['bv'])) {
            $destUrl = EYE_DOMAIN_HTTPS_PE . "user/register?{$query_string}";
        } else { // 大相机统一页面
            $destUrl = EYE_DOMAIN_HTTPS_PE . "userinfo/set?{$query_string}";
        }
        Logger::info("openid: {$openid} url: {$destUrl}", 'hxt_callback');
        header("Location: {$destUrl}");
        exit;
    }
}
