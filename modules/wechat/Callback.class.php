<?php

namespace Air\Modules\Wechat;

use Air\Libs\Base\Utilities;
use \Air\Package\Checklist\CheckInfo;
use \Air\Package\Wechat\WXUtil;
use \Air\Package\Wechat\WechatUserCheck;
use \Air\Package\Wechat\WechatMsgTemplate;
use \Air\Package\Cache\RedisCache;
use \Air\Package\Barcode\Barcode;
use \Air\Package\User\PatientCode;
use \Air\Package\Admin\HandleAlarm;
use Air\Package\Bisheng\BishengUtil;
use Air\Package\Checklist\CheckInfoUtil;
use Air\Package\Checklist\CheckLog;
use Air\Package\Checklist\Helper\RedisCount;
use Air\Package\Checklist\Helper\RedisLock;
use \Air\Package\Fd16\CameraHandler;
use \Air\Package\Fd16\DeviceVersion;
use \Air\Package\Smb\SnPcode;
use Air\Package\Thirdparty\ThirdHandler;
use Air\Package\User\Helper\DBPatientCodeExtraHelper;
use \Air\Package\User\Organizer;
use Air\Package\User\User;
use Air\Package\User\VerificationCode;
use Air\Package\Wechat\WechatMedia;
use \Phplib\Tools\Logger;

class Callback extends \Air\Libs\Controller
{
    const SWITCHS = 0;
    static private $our_openids = [
        'oI5hivxp-QNCoIaRChERPuDSPaAE' => 1, //CHL AI
        'oTS0h570gCIw1K3p4vwqQOmz9c5U' => 1, //CHL AK
    ];

    public function run()
    {
        $token = WX_TOKEN;
        $request = $this->request;
        $timestamp = $request->REQUEST['timestamp'];
        $echostr   = $request->REQUEST['echostr'];
        $signature = $request->REQUEST['signature'];
        $nonce     = $request->REQUEST['nonce'];
        $openid    = $request->REQUEST['openid'];
        $array = array($nonce, $timestamp, $token);
        sort($array, SORT_STRING);
        $this->openid = $openid;
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1(implode('', $array));
        if ($str == $signature) {
            $info = '';
            $postArr = file_get_contents("php://input");
            $this->post = $postObj = simplexml_load_string($postArr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $toUser = (string)$postObj->FromUserName;
            $fromUser = (string)$postObj->ToUserName;
            if (strpos($toUser, WX_OPENID_PREFIX) === 0) {
                define('IS_NEW_WX', 0);
            } else {
                define('IS_NEW_WX', 1);
            }
            $time = (string)$postObj->CreateTime;
            $eventKey = (string)$postObj->EventKey;
            Logger::error("wechat_event_msg:{$openid};postObj:" . json_encode($postObj), 'wechat_callback');
            //判断该数据包是否是订阅的事件推送
            if (strtolower($postObj->MsgType) == 'event') {
                //如果是关注 subscribe 事件
                if (strtolower($postObj->Event) == 'subscribe') {
                    //回复用户消息(纯文本格式)
                    if (strpos('pre' . $eventKey, 'channel_IVAK_GETCODE_NEW') || strpos('pre' . $eventKey, 'NOID_FD16')) {
                        $prefix = $this->getCodePrefix($eventKey);
                        $info = $this->getPatientInfoLink($prefix);
                    } elseif (strpos('pre' . $eventKey, 'IVAK_GETCODE_HOSPITAL_ORG')) {
                        $org_id = explode('channel_IVAK_GETCODE_HOSPITAL_ORG_', $eventKey)[1];
                        $prefix = $this->getCodePrefix('IVAK_GETCODE_HOSPITAL_ORG', $org_id);
                        $info = $this->getPatientInfoLink($prefix, '', $org_id);
                    } elseif (strpos('pre' . $eventKey, 'IVAK_GETCODE_TIYAN_')) { // BAEQ-3130
                        $prefix = $this->getCodePrefix($eventKey);
                        $info = $this->getPatientInfoLink($prefix);
                    } elseif (strpos('pre' . $eventKey, 'IVAK_GETCODE_HOSPITAL')) {
                        $info = $this->handleHospitalSearchLink();
                    } elseif (strpos('pre' . $eventKey, 'SMB_')) {
                        // FD16 慧心瞳
                        $info = $this->handleFD16($eventKey);
                    } elseif (!empty($eventKey)) {
                        $info = $this->handleTextMsg();
                    } else {
                        $msg = '欢迎关注慧心瞳视网膜报告。如果您对您的报告有任何问题，回复消息可以咨询我们的客服人员。';
                        if (IS_NEW_WX) {
                            $msg = '欢迎关注爱康慧心瞳视网膜报告。如果您对您的报告有任何问题，回复消息可以咨询我们的客服人员。';
                        }
                        $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
                    }
                } elseif ($postObj->Event == 'SCAN') {
                    if (strpos('pre' . $eventKey, 'channel_IVAK_GETCODE_NEW') || strpos('pre' . $eventKey, 'NOID_FD16')) {
                        $prefix = $this->getCodePrefix($eventKey);
                        $info = $this->getPatientInfoLink($prefix);
                    } elseif (strpos('pre' . $eventKey, 'IVAK_GETCODE_HOSPITAL_ORG')) {
                        $arr = explode('_', $eventKey);
                        $org_id = array_pop($arr);
                        $prefix = $this->getCodePrefix('IVAK_GETCODE_HOSPITAL_ORG', $org_id);
                        $info = $this->getPatientInfoLink($prefix, '', $org_id);
                    } elseif (strpos('pre' . $eventKey, 'IVAK_GETCODE_HOSPITAL')) {
                        $info = $this->handleHospitalSearchLink();
                    } elseif (strpos('pre' . $eventKey, 'IVAK_GETCODE_TIYAN_')) { // BAEQ-3130
                        $prefix = $this->getCodePrefix($eventKey);
                        $info = $this->getPatientInfoLink($prefix);
                    } elseif (strpos('pre' . $eventKey, 'SMB_')) {
                        $info = $this->handleFD16($eventKey);
                    } else {
                        $info = $this->handleTextMsg();
                    }
                } elseif ($postObj->Event == 'TEMPLATESENDJOBFINISH') {
                    $info = $this->handleTEMPLATESENDJOBFINISH();
                } elseif ($postObj->Event == 'CLICK') {
                    $eventKey = $postObj->EventKey;
                    if ($eventKey == 'HELLO_AIRDOC') {
                        $info = $this->handleAutoResponse(1);
                    } elseif ($eventKey == 'IVAK_GETCODE') {
                        $info = $this->handleGetCode();
                    } elseif ($eventKey == 'IVAK_GETCODE_NEW') {
                        $info = $this->getPatientInfoLink('click');
                    } else {
                        $info = "";
                    }
                }
            } elseif (strtolower($postObj->MsgType) == 'text') {
                $info = 'success';
                if (self::SWITCHS) {
                    $info = $this->handleAutoResponse();
                }
            } else {
                $info = $echostr;
            }
        } else {
            Logger::error("wechat_event_sign_error", 'wechat_callback');
        }
        echo $info;
        exit;
    }

    private function getCodePrefix($eventKey, $org_id = 0)
    {
        $channel = $eventKey;
        if (strpos('pre' . $eventKey, 'channel_')) {
            $key_info = explode('hannel_', $eventKey);
            $channel = $key_info[1];
        }
        ////////////////////////////后登记、先登记、先登记不推送
        $PREFIX_8989 = [
            'IVAK_GETCODE',
            'IVAK_GETCODE_NEW',
            'IVAK_GETCODE_NEW_NOTPUSH',
            'IVAK_GETCODE_NEW_INSURANCE'
        ];
        $PREFIX_8990 = [
            'IVAK_GETCODE_NEWB_DR'
        ];
        $PREFIX_8991 = [
            'IVAK_GETCODE_B',
            'IVAK_GETCODE_NEWB',
            'IVAK_GETCODE_NEW_NOTPUSH_B',
            'IVAK_GETCODE_NEW_INSURANCE_B',
            'IVAK_GETCODE_NEW_INSURANCE_V2_B',
            'IVAK_GETCODE_NEW_INSURANCE_V2_B_ID',
            'IVAK_GETCODE_NEWB_NOID',
            'IVAK_GETCODE_NEW_INSURANCE_V2_B_SIMPLE',
            'IVAK_GETCODE_HOSPITAL_ORG',
            'IVAK_GETCODE_TIYAN_ID',
            'IVAK_GETCODE_TIYAN_NOID',
        ];
        $PREFIX_8992 = [
            'IVAK_GETCODE_NEWB_BV',
            'IVAK_GETCODE_NEWB_BV_TIBET'
        ];
        $PREFIX_8992_1 = [
            'IVAK_GETCODE_C',
            'IVAK_GETCODE_NEWC',
            'IVAK_GETCODE_NEW_NOTPUSH_C',
            'IVAK_GETCODE_NEW_INSURANCE_C',
            'IVAK_GETCODE_NEW_INSURANCE_V2_C',
            'IVAK_GETCODE_NEW_INSURANCE_V2_C_ID',
            'IVAK_GETCODE_NEWC_NOID',
            'IVAK_GETCODE_NEW_INSURANCE_V2_C_SIMPLE'
        ];
        $PREFIX_8996 = [
            'IVAK_GETCODE_G',
            'IVAK_GETCODE_NEWG',
            'IVAK_GETCODE_NEW_INSURANCE_V3_G',
            'IVAK_GETCODE_NEW_INSURANCE_V3_G_ID',
            'IVAK_GETCODE_NEW_NOTPUSH_G',
            'IVAK_GETCODE_NEW_INSURANCE_G',
            'IVAK_GETCODE_NEW_INSURANCE_V2_G',
            'IVAK_GETCODE_NEW_INSURANCE_V2_G_ID',
            'IVAK_GETCODE_NEWG_NOID',
            'IVAK_GETCODE_NEW_INSURANCE_V2_G_SIMPLE'
        ];
        $PREFIX_8993 = [
            'IVAK_GETCODE_D',
            'IVAK_GETCODE_NEWD',
            'IVAK_GETCODE_NEW_NOTPUSH_D',
            'IVAK_GETCODE_NEW_INSURANCE_D',
            'IVAK_GETCODE_NEW_INSURANCE_V2_D',
            'IVAK_GETCODE_NEW_INSURANCE_V2_D_ID',
            'IVAK_GETCODE_NEWD_NOID',
            'IVAK_GETCODE_NEW_INSURANCE_V2_D_SIMPLE'
        ];
        $PREFIX_8994 = [
            'IVAK_GETCODE_E',
            'IVAK_GETCODE_NEWE',
            'IVAK_GETCODE_NEW_NOTPUSH_E',
            'IVAK_GETCODE_NEW_INSURANCE_E',
            'IVAK_GETCODE_NEW_INSURANCE_V2_E',
            'IVAK_GETCODE_NEW_INSURANCE_V2_E_ID',
            'IVAK_GETCODE_NEWE_NOID',
            'IVAK_GETCODE_NEW_INSURANCE_V2_E_SIMPLE'
        ];
        // <el-option label="基础套餐A" value="0" />
        // <el-option label="优悦套餐B" value="1" />
        // <el-option label="经典套餐C" value="2" />
        // <el-option label="尊享套餐D" value="3" />
        // <el-option label="智享套餐" value="4" />
        // <el-option label="慢病套餐" value="6" />
        // <el-option label="糖网套餐" value="7" />
        $org = [];
        if ($org_id) {
            $organizer = new Organizer();
            $org = $organizer->getOrganizerById($org_id);
        }
        if ($org) {
            $type2prefix = [
                0 => '8991',
                1 => '8991',
                2 => '8992',
                3 => '8993',
                4 => '8993',
                6 => '8996',
                7 => '8990',
            ];
            $prefix = $type2prefix[$org['type']];
        } elseif (in_array($channel, $PREFIX_8989)) {
            $prefix = '8989'; // 默认使用机构套餐
        } elseif (in_array($channel, $PREFIX_8990)) {
            $prefix = '8990';  // 糖网 DR
        } elseif (strpos($eventKey, 'NEWB_NOID_FD16') || in_array($channel, $PREFIX_8992)) {
            $prefix = '8992'; // 方案C
        } elseif (in_array($channel, $PREFIX_8991)) {
            $prefix = '8991'; // 方案B
        } elseif (strpos('pre' . $eventKey, 'SMB') || in_array($channel, $PREFIX_8996)) {
            // FD16 prefix is SMB
            $prefix = '8996'; //  标准慢病
        } elseif (in_array($channel, $PREFIX_8992_1)) {
            $prefix = '8992'; // 方案C
        } elseif (in_array($channel, $PREFIX_8993)) {
            $prefix = '8993'; // 方案D
        } elseif (in_array($channel, $PREFIX_8994)) {
            $prefix = '8994'; // 方案D1
        }
        return $prefix;
    }

    private function handleGetCode($prefix = '8989')
    {
        $toUser = $this->post->FromUserName;
        $fromUser = $this->post->ToUserName;
        $time = $this->post->CreateTime;
        if (RedisCache::getCache($toUser, "Screen_Code:")) {
            $template = WechatMsgTemplate::MSG_COMMON_TEXT;
            $msg = "请一分钟后再获取筛查二维码";
            $info = sprintf($template, $toUser, $fromUser, $time, $msg);
            Logger::error("wechat_event_msg:" . $info, 'wechat_callback');
            return $info;
        }
        $template = WechatMsgTemplate::MSG_COMMON_IMAGE;
        $openid = (string) $toUser;
        $code = PatientCode::getFreeCodeByOpenid($openid, $prefix, IS_NEW_WX);
        if (!$code) {
            list($id, $code) = PatientCode::initCode($openid, $prefix, 0, 0, IS_NEW_WX);
        }
        $qrcode_img_file = '/tmp/qr_' . $code . '.png';
        $barcode_img_file = '/tmp/bar_' . $code .  '.png';
        Barcode::generateLocalQrCodeImage($code, $qrcode_img_file);
        Barcode::generateLocalBarcodeImage($code, $barcode_img_file);
        RedisCache::setCache($toUser, $code, 'Screen_Code:', 60);
        $temp_img_file = '/tmp/temp_' . $code . '.png';
        $wx_util = IS_NEW_WX ? new WXUtil(WX_APPID_NEW, WX_SECRET_NEW) : new WXUtil(WX_APPID, WX_SECRET);
        WXUtil::generateScreenImage(['qrcode' => $qrcode_img_file, 'barcode' => $barcode_img_file], $code, $temp_img_file);
        $media_id = $wx_util->uploadImageMedia($temp_img_file);
        $info = sprintf($template, $toUser, $fromUser, $time,  $media_id);
        unlink($barcode_img_file);
        unlink($temp_img_file);
        Logger::error("wechat_event_msg:" . $info, 'wechat_callback');

        $check_log_remark = ['data' => ['event' => (string)$this->post->Event, 'event_key' => (string)$this->post->EventKey]];
        $check_log_remark['lang'] = "点击了自定义菜单，获取检查码二维码";
        CheckLog::addLogInfo(0, 'wechat_callback_event', $check_log_remark, 0, '', $code);

        return $info;
    }

    private function getPatientInfoLink($prefix = '8989', $sn = '', $org_id = 0, $params = [])
    {
        $params = $this->formatParams($params);
        $plain_sn = "";
        $toUser = $this->post->FromUserName;
        $fromUser = $this->post->ToUserName;
        $time = $this->post->CreateTime;
        $eventKey = (string) $this->post->EventKey;
        $event = (string) $this->post->Event;
        $openid = (string) $toUser;
        // BAEQ-3324
        $is_ak_outside = 0;
        if (strpos($eventKey, 'IVAK_GETCODE_NEW')) {
            $org_id = 5001;
            $is_ak_outside = 1;
        }
        if (RedisCache::getCache($toUser, "Screen_Code:")) {
            $template = WechatMsgTemplate::MSG_COMMON_TEXT;
            $msg = "请一分钟后再获取筛查二维码";
            $info = sprintf($template, $toUser, $fromUser, $time, $msg);
            Logger::error("wechat_event_msg:" . $info, 'wechat_callback');
            return $info;
        }
        $type = 0;
        //click from history
        if ($prefix == 'click') {
            $prefix = '8991';
            $old_pitem = PatientCode::getItemsByOpenid($openid, 0);
            if (substr($old_pitem['pcode'], 0, 2) == '89') {
                $prefix = substr($old_pitem['pcode'], 0, 4);
            }
            $type = $old_pitem['type'];
        } elseif (strpos($eventKey, 'GETCODE_NEW_INSURANCE_V2')) {
            $type = 2;
            if (strpos($eventKey, '_SIMPLE')) {
                $type = 3;
            }
        } elseif (strpos($eventKey, 'INSURANCE')) {
            $type = 1;
        }
        $org = $user = [];
        if ($sn) {
            $camera = CameraHandler::getCameraBySN($sn);
            if ($camera['status']) {
                $msg = '您所使用的健康扫描仪已经停止工作，请您联系现场的工作人员或拨打400-100-3999';
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                return $str;
            }
            $camera_env = DeviceVersion::getCameraEnv($camera['sn']);
            $camera_env = $camera_env ? 'test' : 'production';
            if ($camera_env !== ENV) {
                $msg = '您所使用的健康扫描仪环境配置错误，请您联系现场的工作人员或拨打400-100-3999';
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                return $str;
            }
            $plain_sn = $camera['sn'];
            $user_id = $camera['user_id'];
            if (!$user_id) {
                $msg = '👉相机未绑定账号，请先绑定到账号！';
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                return $str;
            }
            $u_obj = new User();
            $user = $u_obj->getUserById($user_id);
            $package_type = $user['org']['type'];
            $prefix = PatientCode::$package_prefix[$package_type] ?? $prefix;
            //使用第三方公众号启动相机
            if (WXUtil::thirdQrMatchReport($user['org_id'], 'huixintong')) {
                $msg = '亲，您扫错二维码了，请联系工作人员获取正确的二维码。';
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                return $str;
            }
        }
        $code = PatientCode::getFreeCodeByOpenid($openid, $prefix, $type, IS_NEW_WX);
        if (!$code) {
            $not_push = strpos($eventKey, 'NOTPUSH') ? 1 : 0;
            list($id, $code) = PatientCode::initCode($openid, $prefix, $not_push, $type, IS_NEW_WX);
            //记录鹰瞳收费宝参数
            if ($params['type'] == 'DC') {
                $params['patient_code_id'] = $id;
                DBPatientCodeExtraHelper::create($params);
            }
        }


        $check_log_remark = ['data' => ['event' => $event, 'event_key' => $eventKey, 'sn' => $plain_sn ?? '']];
        if (strtolower($event) == 'subscribe') {
            $check_log_remark['lang'] = "扫码关注了公众号";
        } elseif ($event == 'SCAN') {
            $check_log_remark['lang'] = "扫码进入了公众号";
        } elseif ($event == 'CLICK') {
            $check_log_remark['lang'] = "点击了自定义菜单";
        }
        CheckLog::addLogInfo(0, 'wechat_callback_event', $check_log_remark, $user_id, '', $code);

        if ($sn) {

            if ($plain_sn) {
                SnPcode::createSnPcode(['pcode' => $code, 'sn' => $plain_sn, 'user_id' => $user_id]);
            }

            // 判断用户扫描与机构报告是否一致
            $qr_match_report = WXUtil::qrMatchReport($user['org']['customer_id'], 'huixintong');
            if (!$qr_match_report['error_code']) {
                if (in_array($user['org_id'], ThirdHandler::ORG_IDS['taiping'])) {
                    $url = ThirdHandler::getTaipingQRUrl($camera['sn']);
                    $msg = '👉<a href="' . $url . '">请点击开始检测（太平用户专属）</a>';
                    Logger::info($camera['sn'], 'taiping_qrcode_info');
                    $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                    return $str;
                } else if ($user['org_id'] != SKB_ORG_ID) {
                    $media_id = WechatMedia::SCAN_QRCODE_TIPS[ENV]['huixintong'][$qr_match_report['report_type']];
                    if (empty($media_id)) {
                        $msg = '亲，您扫错二维码了，请联系工作人员获取正确的二维码。';
                        $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                    } else {
                        $str = sprintf(WechatMsgTemplate::MSG_COMMON_IMAGE, $toUser, $fromUser, time(), $media_id);
                    }
                    return $str;
                }
                $auth_user = CameraHandler::getAuthUser($camera['sn']);
                $auth_phone = $auth_user ? $auth_user['phone'] : '-';
                $auth_name = $auth_user ? $auth_user['name'] : '-';
                $msg = '亲，您扫错二维码了，请联系工作人员获取正确的二维码！';
                if ($user['org_id'] == SKB_ORG_ID) {
                    $msg = '亲，本产品的测试阶段已结束，无法体验了哦~';
                }
                Utilities::DDMonitor("P3-pangu-【OP】SN: {$camera['sn']}  是机构名称：{$user['org']['name']}, 账号：{$user['name']}，授权手机号：{$auth_phone}，联系人：{$auth_name}, 用户扫错二维码（扫了{$qr_match_report['qr_report_name']}二维码），请跟进。", 'bigop', WXUtil::OP_PHONES);
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                return $str;
            }
            PatientCode::updateUserInfo($code, $user['org_id'], $user_id);
            if ($user) {
                $org = $user['org'];
            }
        } else if ($org_id) {
            $organizer = new Organizer();
            $org = $organizer->getOrganizerById($org_id);
        }

        //设置语言处理
        $report_lang = '';
        if ($org) {
            $report_lang = $org['config']['report_lang'];
            if ($report_lang) {
                Utilities::setI18n($report_lang);
                $report_lang = Utilities::getLocale($report_lang);
            }
        }

        $wx_util = IS_NEW_WX ? new WXUtil(WX_APPID_NEW, WX_SECRET_NEW) : new WXUtil(WX_APPID, WX_SECRET);
        $template_id = IS_NEW_WX ? WX_REGISTER_TEMPLATE_ID_NEW : WX_REGISTER_TEMPLATE_ID;

        $is_miniprogram = 1;
        $url = EYE_DOMAIN . 'api/wechat/jump?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . '&is_new=' . IS_NEW_WX;
        if (in_array($user['org_id'], PUHUIBAO_ORG_IDS) && $camera['sn']) {
            $url = EYE_DOMAIN . 'h5-v2/scanProcess/index?activityNo=BrcROe76V-cbki-SHY2SU&sn=' . $camera['sn'] . '&openId=' . $openid;
            $msg = '<a href="' . $url . '">👉点击此处填写信息>></a>';
            $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
            WechatUserCheck::sendRegisterMiniprogram($wx_util, $template_id, $openid, $url, '鹰瞳医疗');
            $info = '';
            Logger::info('openid= ' . $this->request->REQUEST['openid'] . ' url=' . $url . ' mini:' . WXUtil::h5Url2Miniprogram($url), 'hxt_callback');
            return $info;
        } elseif (in_array($org_id, ThirdHandler::ORG_IDS['register'])) {
            // 注册组收集数据 1260
            // todo 修改地址
            $url = EYE_DOMAIN . 'h5-v2/dataCollection?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . '&org_id=' . $org_id;
            $msg = '<a href="' . $url . '">👉点击此处填写信息>></a>';
            $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);

            Logger::info('openid= ' . $this->request->REQUEST['openid'] . ' url=' . $url . ' mini:' . WXUtil::h5Url2Miniprogram($url), 'hxt_callback');
            return $info;
        } elseif (in_array($org_id, ThirdHandler::ORG_IDS['hongmei'])) {
            // 虹梅社区科研定制问卷 判断用户是第几次检查
            // todo 修改地址
            $url = 'pages/hongMei/question?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . '&org_id=' . $org_id;
        } elseif (strpos($eventKey, '_BV_TIBET')) {
            $url .= '&bv=1&noid=0&tibet=1';
            $is_miniprogram = 0; // 西藏科研不支持小程序
        } elseif (strpos($eventKey, '_BV')) {
            $url .= '&bv=1&noid=1';
            $is_miniprogram = 0; // 科研不支持小程序
        } elseif (strpos($eventKey, 'IVAK_GETCODE_HOSPITAL_ORG')) {
            $url = EYE_DOMAIN . 'api/wechat/jump?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . '&org_id=' . $org_id . '&is_new=' . IS_NEW_WX;
            if (in_array($org_id, [40104, 40143]) && ENV == 'production') {
                $is_miniprogram = 0; // 上海妇婴, 山东妇婴
            } else {
                if ($org['age_type'] != 2) {
                    $url .= '&noid=1';
                }
            }
        } elseif (strpos($eventKey, '_NOID_FD16') || strpos('pre' . $eventKey, 'SMB')) {
            // FD16 慧心瞳
            // 鹰瞳收费宝标记DC
            if (strpos($eventKey, '_DC_')) {
                $arr = explode('_DC_', $eventKey);
                $sn = array_pop(explode('_', $arr[0]));
            } else {
                $sn = array_pop(explode('_', $eventKey));
            }
            $camera = CameraHandler::getCameraBySN($sn);
            $plain_sn = $camera['sn'];
            $user_id = $camera['user_id'];
            $user_obj = new User();
            $user = $user_obj->getUserById($user_id);
            $show_fd16_video_str = intval($user['org']['config']['show_fd16_video']) === 1 ? '&show_fd16_video=1' : '';
            $show_fd16_qrcode_str = intval($user['org']['config']['show_fd16_qrcode']) === 1 || $camera['work_mode'] == 4 ? '&show_fd16_qrcode=1' : '';
            $pay_str = '';
            if ($user['org_id'] == 1) {
                if (trim($user['config']['pay_config']) && is_numeric($user['config']['pay_config']) && bccomp($user['config']['pay_config'], 0.01, 2) >= 0) {
                    $pay_str = "&pay_price={$user['config']['pay_config']}";
                    $is_miniprogram = 0; // 支付不支持小程序
                }
            }
            $work_mode_str = '&work_mode=' . $camera['work_mode'];
            $age_type = $user['org']['age_type'];
            $work_mode_str .= (in_array($user['org']['customer_id'], ZY_CUSTOMER_IDS) ? '&is_zhongyou=1' : '');
            $url = EYE_DOMAIN . 'api/wechat/jump?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . '&noid=1&is_fd16=1&is_new=' . IS_NEW_WX . "&sn={$sn}&age_type={$age_type}{$pay_str}";
            if ($user['org_id'] == TIBET_ORG_ID) {
                $url .= '&bv=1&noid=0&tibet=1';
                $is_miniprogram = 0; // 西藏科研不支持小程序
            }
            if (in_array($user['org_id'], ARMY_ORG_ID)) {
                $url .= '&a=1';
                $is_miniprogram = 0; // army不支持小程序
            }
            $register_type = ($user['config']['register_type'] == -1 || !isset($user['config']['register_type'])) ? $user['org']['config']['register_type'] : $user['config']['register_type'];
            $camera['work_mode'] == 4 && $register_type = 1;
            $register_type_str = '&register_type=' . $register_type;
            $url .= $show_fd16_video_str . $show_fd16_qrcode_str . $work_mode_str . $register_type_str;
        } elseif (strpos($eventKey, '_NOID')) {
            $url = EYE_DOMAIN . 'api/wechat/jump?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . '&noid=1&is_new=' . IS_NEW_WX;
        } elseif (strpos('h' . $eventKey, 'IVAK_GETCODE_NEW_INSURANCE_V3_G')) {
            $url = EYE_DOMAIN . 'api/wechat/jump?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . '&ins_v2=3&is_new=' . IS_NEW_WX;
            if (strpos($eventKey, '_ID')) {
                $url .= '&req_id=1';
            }
            $is_miniprogram = 0; // 云南太平不支持小程序
        } elseif ($type === 1) {
            $url = EYE_DOMAIN . 'user/ins/register?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . '&insurance=1';
            $is_miniprogram = 0; // 非jump不支持小程序
        } elseif ($type === 2) {
            $url = EYE_DOMAIN . 'api/wechat/jump?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . '&ins_v2=1' . '&is_new=' . IS_NEW_WX;
            if (strpos($eventKey, '_ID')) {
                $url .= '&req_id=1';
            }
            $is_miniprogram = 0; // ins_v2不支持小程序
        }
        $show_pay_page = $user['show_pay_page'] == -1 ? $user['org']['config']['show_pay_page'] : $user['show_pay_page'];
        if ($sn || $plain_sn) {
            if ($sn) {
                $bisheng_camera = CameraHandler::getCameraBySNOrMd5($sn);
            } else {
                $bisheng_camera = CameraHandler::getCameraBySNOrMd5($plain_sn);
            }
            if ($bisheng_camera['sn']) {
                $BishengUtil = new BishengUtil();
                $bisheng_config = $BishengUtil->getConfigByDevice($bisheng_camera['sn']);
                if (!empty($bisheng_config)) {
                    $show_pay_page = $bisheng_config['show_pay_page'];
                }
            }
        }
        if ($show_pay_page) {
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
            if (in_array($org_id, TAIKANG_ORG_ID)) {
                $price = 598;
            }
            $url .= '&jump_to_payment=1&price=' . $price . '&origin_price=' . $origin_price;
            if ($show_pay_page == 2 || in_array($user['org_id'], VCODE_ORG_ID)) {
                $vcode_str = '';
                $today_finished_num = 0;
                $vcode_str = "&vcode=1";
                if (in_array($user['org_id'], VCODE_ORG_ID)) {
                    $vcode_str .= "&jump_authorize=1";
                }
                $today_finished_num = RedisCount::getCount(date('ymd') . "_vcode_", $openid);
                // 福利码模式，同一手机启动3次
                $throt = ENV == 'test' ? 50 : 3;
                if ($today_finished_num >= $throt) {
                    $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), '您今天已经扫描多次，请明天继续。');
                    return $info;
                }
                $url .= $vcode_str;
            }
            if (CameraHandler::checkAgentNum($sn)) {
                $url .= '&check_agent_num=1';
            }
        }
        if (isset($org['age_type'])) { // AK-1230：(0 || null)填写生日;1填写年龄
            $url .= ('&age_type=' . $org['age_type']);
        }
        if ($user['org_id'] == PA_ZY_ORG_ID && PA_SWITCH == 1 || $user['org_id'] == PA_APP_ORG_ID && PA_APP_SWITCH == 1) {
            $effective_check_infos = CheckInfoUtil::getEffectiveCheckInfoByOpenid($openid, PA_ALL_ID);
            if (!$effective_check_infos) {
                $url .= '&jump_authorize=1';
            }
        }
        //微信推送点击链接添加一个参数substr6Sn，值为sn解密之后的后6位
        if ($plain_sn) {
            $substr6Sn = substr($plain_sn, -6);
            $url .= "&substr6Sn={$substr6Sn}";
        }
        // 判断设备类型
        $model = isset($camera['model']) ? $camera['model'] : '';
        $url .= "&model={$model}";
        if ($model == CameraHandler::MODEL_AI_FD16) {
            $url .= "&show_fd16_video=0";
        }
        // BAEQ-3130
        if (strpos('pre' . $eventKey, 'IVAK_GETCODE_TIYAN_')) {
            $url = str_replace('api/wechat/jump', 'userinfo/set', $url);
            $url .= "&is_tiyan=1&ty_title=体验码";
            RedisCache::setCache('pcode_openid_' . $code, $openid, '', 86400);
        } elseif ($org_id && !strpos($url, 'org_id=')) { // BAEQ-3324
            $url .= "&org_id=$org_id";
        }
        if ($is_ak_outside) {
            $url .= "&is_ak_outside=1";
        }

        if ($report_lang) {
            $url .= '&language=' . $report_lang;
        } else {
            $url .= '&language=zh_CN';
        }

        // CP-670 SME近视防控
        if (!$org && isset($user['org'])) {
            $org = $user['org'];
        }
        if ($org) {
            if (isset($org['id']) && !strpos($url, 'org_id=') && !$is_ak_outside) {
                $url .= "&org_id=".$org['id'];
            }
            
            if (isset($org['config']['sme_config_is_sme_org']) && intval($org['config']['sme_config_is_sme_org']) > 0) {
                $url .= '&is_sme_org=' . $org['config']['sme_config_is_sme_org'];
                if (isset($org['config']['check_quantify_config']) && strpos($org['config']['check_quantify_config'], 'myopic_refraction') !== false) {
                    $url .= '&require_sphere=1';
                }
            }
        }

        if (defined('SWITCH_REGISTER_MINIPROGRAM') && (strpos('pre' . $eventKey, 'IVAK_GETCODE_TIYAN_') /*BAEQ-3130*/ || $org && $org['config']['rigister_miniprogram'] || $user['org']['config']['rigister_miniprogram'] || SWITCH_REGISTER_MINIPROGRAM) && $is_miniprogram) {
            if (0 && ENV == 'test') {
                $msg = '<a data-miniprogram-appid=' . REGISTER_WX_APPID . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '">点击此处填写信息>></a>';
                Logger::info('test_openid= ' . $this->request->REQUEST['openid'] . ' url=' . $url . ' mini:' . WXUtil::h5Url2Miniprogram($url), 'hxt_callback');
                $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
            } else {
                $product = '慧心瞳';
                // BAEQ-3764
                if ($user && $user['org']) {
                    $org = $user['org'];
                }
                // 鹰瞳医疗Pro、东海版普通、左右眼分开
                if ($org && (in_array($org['new_template'], [15, 17, 18, 20]) || $org['list_in_home'] == 1 || $org['list_in_home'] == 5)) {
                    $product = '鹰瞳医疗';
                    if (stripos($org['name'], 'icvd') || $org['config']['business_line'] == 3) {
                        $product = '鹰瞳医疗-ICVD';
                    } elseif ($org['config']['business_line'] == 4) {
                        $product = '鹰瞳医疗-MV';
                    } elseif ($org['config']['business_line'] == 5) {
                        $product = '鹰瞳健康-MV';
                    }
                }
                if (ENV == 'test') {
                    $msg = '<a data-miniprogram-appid=' . REGISTER_WX_APPID . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '">' . gettext('点击此处填写信息') . '>></a>';
                    Logger::info('test_openid= ' . $this->request->REQUEST['openid'] . ' url=' . $url . ' mini:' . WXUtil::h5Url2Miniprogram($url), 'hxt_callback');
                    $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
                } else {
                    WechatUserCheck::sendRegisterMiniprogram($wx_util, $template_id, $openid, WXUtil::h5Url2Miniprogram($url), $product, REGISTER_WX_APPID);
                    $info = '';
                }
            }
        } else {
            $url = $wx_util->getRedirectUrl($url, IS_NEW_WX);
            $msg = '<a href="' . $url . '">👉' . gettext('点击此处填写信息') . '>></a>';
            $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
        }
        Logger::info('openid= ' . $this->request->REQUEST['openid'] . ' url=' . $url . ' mini:' . WXUtil::h5Url2Miniprogram($url), 'hxt_callback');
        return $info;
    }

    private function handleHospitalSearchLink()
    {
        $toUser = $this->post->FromUserName;
        $fromUser = $this->post->ToUserName;
        $time = $this->post->CreateTime;
        $openid = (string) $toUser;
        $wx_util = IS_NEW_WX ? new WXUtil(WX_APPID_NEW, WX_SECRET_NEW) : new WXUtil(WX_APPID, WX_SECRET);
        $url = EYE_DOMAIN . 'api/wechat/jump?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&frm=hospital_search&is_new=' . IS_NEW_WX;
        $url = $wx_util->getRedirectUrl($url, IS_NEW_WX);
        $msg = '查询报告请<a href="' . $url . '">点击这里>></a>';
        $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
        Logger::error("event=scan_qr_" . IS_NEW_WX . " open_id={$openid}", 'wechat_search');

        return $info;
    }

    private function handleTextMsg()
    {
        $msg = '您的视网膜慧心瞳筛查报告正在生成中。报告生成后，您将收到一条微信通知。';
        $toUser = $this->post->FromUserName;
        $fromUser = $this->post->ToUserName;
        $time = $this->post->CreateTime;
        $language = '';
        if (strpos('pre' . $this->post->EventKey, 'uuid_')) {
            $uuid = array_pop(explode('_', $this->post->EventKey));
            PatientCode::updateOpenidbyPcode($uuid, $this->openid);
            $pcode_item = PatientCode::getItemByPcode($uuid);
            $check_id = $pcode_item['check_id'];
        } elseif (strpos('pre' . $this->post->EventKey, 'vcode_')) {
            $vcode = array_pop(explode('_', $this->post->EventKey));
            $vcode_item = VerificationCode::checkExist($vcode);
            $uuid = $vcode_item['pcode'];
            if ($uuid) {
                PatientCode::updateOpenidbyPcode($uuid, $this->openid);
            }
            $pcode_item = PatientCode::getItemByPcode($uuid);
            $check_id = $pcode_item['check_id'];
        } elseif (strpos('pre' . $this->post->EventKey, 'qrscene_')) {
            $check_id = explode('_', $this->post->EventKey)[1];
            if (strpos('pre' . $this->post->EventKey, 'en_US')) {
                $language = 'en_US';
            }
        } elseif (strpos('pre' . $this->post->EventKey, 'en_US')) {
            $check_id = explode('_', $this->post->EventKey)[0];
            $language = 'en_US';
        } else {
            $check_id = (int) $this->post->EventKey;
        }
        $cobj = new CheckInfo();
        $cobj->setCache(0);
        $cobj->setFromScript(1);
        $check_info = $cobj->getCheckDetail($check_id);
        $check_info = $check_info[0];
        $check_info['language'] = $language;
        list($can_push_report, $can_not_push_report_reasons) = CheckInfoUtil::canPushReport($check_info);
        if ((!$can_push_report || $check_info['patient']['status'] == 0) && $check_info['customer_id'] != 1) {
            $lock = RedisLock::lock('can_not_push_report_callback_' . $check_info['check_id'], 60);
            if ($lock) {
                $content = '报告未生成，原因【' . implode(',', $can_not_push_report_reasons) . '】 上传时间：' . $check_info['created'] . ' 开始评估时间：' . $check_info['start_time'];
                Logger::info($content, 'can_not_push_report', ['check_id' => $check_info['check_id']]);
            }
            $db_status = 0;
            $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
        } else {
            // BAEQ-1331 未本地审核不推送报告
            if (!($check_info['is_retina'] == 2 && $check_info['review_status'] == CheckInfo::REVIEW_DONE)) {
                $ret = WechatUserCheck::sendMsgByOpenId(['open_id' => $this->openid], $check_info, 1);
            }
            $db_status = 1;
            if (!$ret) {
                $db_status = 0;
            }
            $info = '';
        }
        // BAEQ-1331 本地医生审核后签字，但是还没审核
        if ($check_info['is_retina'] == 2 && $check_info['review_status'] == CheckInfo::REVIEW_DONE) {
            $db_status = 2;
            $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
        }
        if (!(ENV == 'production' && isset(self::$our_openids[$this->openid]) && !in_array($check_info['org_id'], [1, 5129]))) {
            $item = WechatUserCheck::addItem(['open_id' => $this->openid, 'check_id' => $check_id, 'status' => $db_status]);
        }
        $alarms = HandleAlarm::getAlarmByCheckIds($check_id);
        // 有警示单，新扫码的人
        if ($alarms && defined('IS_NEW_WX') && $item['open_id'] && !isset($item['updated'])) {
            $alarm = $alarms[$check_id];
            $witem = ['open_id' => $this->openid, 'check_id' => $check_id, 'new_wechat' => IS_NEW_WX];
            $witem['name'] = $check_info['patient']['name'];
            WechatUserCheck::sendWarningMsg($witem, $alarm, $check_info);
        }
        return $info;
    }

    private function handleTEMPLATESENDJOBFINISH()
    {
        $toUser = $this->post->FromUserName;
        $fromUser = $this->post->ToUserName;
        $time = $this->post->CreateTime;
        $msgType = $this->post->MsgType;
        $event = $this->post->Event;
        $status = $this->post->Status;
        $msgId = $this->post->MsgID;
        $template = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Event><![CDATA[%s]]></Event>
            <MsgID>%s</MsgID>
            <Status><![CDATA[%s]]></Status>
            </xml>";
        $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $event, $msgId, $status);
        return $info;
    }

    private function isWorkTime()
    {
        $weekday = date('w');
        $time = date('G');
        if ($time < 8 || $time > 18) {
            return false;
        } else {
            return true;
        }
    }

    private function handleAutoResponse($click = 0)
    {
        if (defined('IS_NEW_WX') && IS_NEW_WX) {
            return '';
        }
        $toUser = $this->post->FromUserName;
        $fromUser = $this->post->ToUserName;
        $time = $this->post->CreateTime;
        if (!$click) {
            $text = trim($this->post->Content);
        } else {
            $text = 'HELLO_AIRDOC';
        }
        // true: a: text; false: b: qr
        $ab = in_array(substr(md5($toUser), 0, 1), [1, 2, 3, 4, 5, 6, 7, 8]);
        if (strpos('pre' . $text, 'aaaa')) {
            $ab = TRUE;
        } elseif (strpos('pre' . $text, 'bbbb')) {
            $ab = FALSE;
        }
        $ab = TRUE;
        $msgType = 'text';
        if ($ab) {
            if ($this->isWorkTime()) {
                Logger::info("[{$toUser}] [$text] [aba_work]", 'wechat_sevice_qr');
                $content = "您好，这里是人工客服，请问有什么可以帮您？";
            } else {
                Logger::info("[{$toUser}] [$text] [aba_out]", 'wechat_sevice_qr');
                $content = "您好，报告解读全国统一电话：400-100-3999，我们的工作时间为周一至周日的8点至19点，现在为非工作时间，请您留言或留下联系方式，我们会在上班后第一时间联系您！";
            }
            ENV == 'test' && $content = "[" . ENV . "]" . $content;
            $template = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                </xml>";
            $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
        } else {
            if ($this->isWorkTime() || strpos('pre' . $text, 'wwww')) {
                Logger::info("[{$toUser}] [$text] [abb_work]", 'wechat_sevice_qr');
                $filename = ROOT_PATH . '/config/assets/service_qywx_qr.png';
            } else {
                Logger::info("[{$toUser}] [$text] [abb_out]", 'wechat_sevice_qr');
                $filename = ROOT_PATH . '/config/assets/service_qywx_qr.png';
            }
            if (strpos('pre' . $text, 'oooo')) {
                $filename = ROOT_PATH . '/config/assets/service_qywx_qr.png';
            }
            $template = WechatMsgTemplate::MSG_COMMON_IMAGE;
            $wx_util = new WXUtil();
            $media_id = $wx_util->uploadImageMedia($filename);
            $info = sprintf($template, $toUser, $fromUser, $time,  $media_id);
            Logger::info($info, 'wechat_sevice_qr');
        }
        return $info;
    }

    private function handleFD16($eventKey)
    {
        // 鹰瞳收费宝标记DC
        if (strpos($eventKey, '_DC_')) {
            $arr = explode('_DC_', $eventKey);
            $sn = array_pop(explode('_', $arr[0]));
            $prefix = $this->getCodePrefix($arr[0]);
        } else {
            $sn = array_pop(explode('_', $eventKey));
            $prefix = $this->getCodePrefix($eventKey);
        }
        $params = explode('_', $eventKey);
        // prefix is 8996
        echo $this->getPatientInfoLink($prefix, $sn, 0, $params);
        return;
    }
    //格式化参数，
    private function formatParams($params = [])
    {
        $data = [];
        // 医生分成参数填充
        if ($params[2] == 'DC') {
            $data['type'] = 'DC';
            $data['sn'] = $params['1']; //设备号
            $data['doctor_mobile'] = $params['3']; //医生手机号
            $data['divide'] = $params['4']; //是否分成
        }
        return $data;
    }
}
