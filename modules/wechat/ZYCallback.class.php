<?php

namespace Air\Modules\Wechat;

use Air\Libs\Base\Utilities;
use \Air\Package\Checklist\CheckInfo;
use \Air\Package\Wechat\WXUtil;
use \Air\Package\Wechat\WechatUserCheck;
use \Air\Package\Wechat\WechatMsgTemplate;
use \Air\Package\Cache\RedisCache;
use \Air\Package\User\PatientCode;
use \Air\Package\Admin\HandleAlarm;
use Air\Package\Checklist\CheckInfoUtil;
use Air\Package\Checklist\CheckLog;
use Air\Package\Checklist\Helper\RedisCount;
use Air\Package\Checklist\Helper\RedisLock;
use \Air\Package\Fd16\CameraHandler;
use \Air\Package\Fd16\DeviceVersion;
use \Air\Package\Smb\SnPcode;
use Air\Package\Thirdparty\ThirdHandler;
use Air\Package\User\User;
use Air\Package\User\VerificationCode;
use Air\Package\Wechat\WechatMedia;
use \Phplib\Tools\Logger;

class ZYCallback extends \Air\Libs\Controller
{
    const SWITCHS = 0;
    static private $our_openids = [
        'oI5hivxp-QNCoIaRChERPuDSPaAE' => 1, //CHL AI
        'o657O0UVWHnUlwNRFAQZQxvBJlXw' => 1, //CHL AK
    ];

    const QINGDAO_TAG_ID = [
        'production' => 100,
        'test' => 100,
    ];

    const QINGDAO_ORG_ID = [
        'production' => 40512,
        'test' => 40512,
    ];

    const QINGDAO_MEDIA_ID = [
        'production' => 'rciOi0ysKllhKWM3E4P8RxaWHBNJ6xOOg6sPcEmoo68',
        'test' => 'h4zL1Z9gzcRkbW-KlkRZkvhsi941nIDZJIYOYSTwvMc',
    ];

    public function run()
    {
        $token = ZY_WX_TOKEN;
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
        $str = sha1(implode($array));
        if ($str == $signature) {
            $info = '';
            $postArr = file_get_contents("php://input");
            $this->post = $postObj = simplexml_load_string($postArr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $toUser = (string)$postObj->FromUserName;
            $fromUser = (string)$postObj->ToUserName;
            define('IS_NEW_WX', 3);
            $time = (string)$postObj->CreateTime;
            $eventKey = (string)$postObj->EventKey;
            Logger::error("wechat_event_msg:{$openid};postObj:" . json_encode($postObj), 'zy_wechat_callback');
            //判断该数据包是否是订阅的事件推送
            if (strtolower($postObj->MsgType) == 'event') {
                //如果是关注 subscribe 事件
                if (strtolower($postObj->Event) == 'subscribe') {
                    //回复用户消息(纯文本格式)
                    if (strpos('pre' . $eventKey, 'SMB_')) {
                        // FD16 众佑
                        $info = $this->handleFD16($eventKey);
                    } elseif (!empty($eventKey)) {
                        $info = $this->handleTextMsg();
                    } else {
                        $msg = '欢迎关注众佑扫描。如果您对您的报告有任何问题，回复消息可以咨询我们的客服人员。';
                        $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
                    }
                } elseif ($postObj->Event == 'SCAN') {
                    if (strpos('pre' . $eventKey, 'SMB_')) {
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
                    } else if ($eventKey == 'QINGDAO_AIRDOC') {
                        $info = sprintf(WechatMsgTemplate::MSG_COMMON_IMAGE, $toUser, $fromUser, $time, self::QINGDAO_MEDIA_ID[ENV]);
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
            Logger::error("wechat_event_sign_error", 'zy_wechat_callback');
        }
        echo $info;
        exit;
    }

    private function getPatientInfoLink($prefix = '8996', $sn = '', $org_id = 0)
    {
        $toUser = $this->post->FromUserName;
        $fromUser = $this->post->ToUserName;
        $time = $this->post->CreateTime;
        $eventKey = (string) $this->post->EventKey;
        $openid = (string) $toUser;
        if (RedisCache::getCache($toUser, "Screen_Code:")) {
            $template = WechatMsgTemplate::MSG_COMMON_TEXT;
            $msg = "请一分钟后再获取筛查二维码";
            $info = sprintf($template, $toUser, $fromUser, $time, $msg);
            Logger::error("wechat_event_msg:" . $info, 'zy_wechat_callback');
            return $info;
        }
        $type = 0;
        $org = $user = [];
        $substr6Sn = "";
        if ($sn) {
            $camera = CameraHandler::getCameraBySN($sn);
            $plain_sn = $camera['sn'];
            $user_id = $camera['user_id'];
            $substr6Sn = substr($plain_sn, -6);
            $u_obj = new User();
            $user = $u_obj->getUserById($user_id);
            if ($user['org_id'] == self::QINGDAO_ORG_ID[ENV]) {
                $wx_obj = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
                $wx_obj->makeTag4User(self::QINGDAO_TAG_ID[ENV], $openid);
            }
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
            if (!$user_id) {
                $msg = '👉相机未绑定账号，请先绑定到账号！';
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                return $str;
            }
            //使用第三方公众号启动相机
            if (WXUtil::thirdQrMatchReport($user['org_id'], 'zhongyou')) {
                $msg = '亲，您扫错二维码了，请联系工作人员获取正确的二维码。';
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                return $str;
            }
            // 判断用户扫描与机构报告是否一致
            $qr_match_report = WXUtil::qrMatchReport($user['org']['customer_id'], 'zhongyou');
            if (!$qr_match_report['error_code']) {
                if (in_array($user['org_id'], ThirdHandler::ORG_IDS['taiping'])) {
                    $url = ThirdHandler::getTaipingQRUrl($camera['sn']);
                    $model = isset($camera['model'])?$camera['model']:'';
                    $url .= "&model={$model}";
                    $msg = '👉<a href="' . $url . '">请点击开始检测（太平用户专属）</a>';
                    Logger::info($camera['sn'], 'taiping_qrcode_info');
                    $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                    return $str;
                } else {
                    $auth_user = CameraHandler::getAuthUser($camera['sn']);
                    $auth_phone = $auth_user ? $auth_user['phone'] : '无';
                    $auth_name = $auth_user ? $auth_user['name'] : '-';
                    $msg = '亲，您扫错二维码了，请联系工作人员获取正确的二维码！';
                    Utilities::DDMonitor("P3-pangu-【OP】SN: {$camera['sn']}, 机构名称：{$user['org']['name']}, 账号为：{$user['name']}，授权手机号：{$auth_phone}，联系人：{$auth_name}, 用户扫错二维码（扫了{$qr_match_report['qr_report_name']}二维码），请跟进。", 'bigop', WXUtil::OP_PHONES);
                    $media_id = WechatMedia::SCAN_QRCODE_TIPS[ENV]['zhongyou'][$qr_match_report['report_type']];
                    if (empty($media_id)) {
                        $msg = '亲，您扫错二维码了，请联系工作人员获取正确的二维码。';
                        $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                    } else {
                        $str = sprintf(WechatMsgTemplate::MSG_COMMON_IMAGE, $toUser, $fromUser, time(), $media_id);
                    }
                    return $str;
                }
            }
            if ($user['org_id'] == PA_ZY_ORG_ID && PA_SWITCH == 2 || $user['org_id'] == PA_APP_ORG_ID && PA_APP_SWITCH == 2) {
                $type = 6;
            }
        }
        $code = PatientCode::getFreeCodeByOpenid($openid, $prefix, $type, IS_NEW_WX);
        if (!$code) {
            list($id, $code) = PatientCode::initCode($openid, $prefix, 0, $type, IS_NEW_WX);
        }

        $check_log_remark = ['data' => ['event' => (string)$this->post->Event, 'event_key' => (string)$this->post->EventKey, 'camera' => $camera ?? '', 'openid' => $openid, 'pcode' => $code]];
        if (strtolower($this->post->Event) == 'subscribe') {
            $check_log_remark['lang'] = "扫码关注了公众号";
        } elseif ($this->post->Event == 'SCAN') {
            $check_log_remark['lang'] = "扫码进入了公众号";
        } elseif ($this->post->Event == 'CLICK') {
            $check_log_remark['lang'] = "点击了自定义菜单";
        }
        CheckLog::addLogInfo(0, 'wechat_zy_callback_event', $check_log_remark, 0, '', $code);

        if ($sn) {
            if ($plain_sn) {
                SnPcode::createSnPcode(['pcode' => $code, 'sn' => $plain_sn, 'user_id' => $user_id]);
            }
            PatientCode::updateUserInfo($code, $user['org_id'], $user_id);
            if ($user) {
                $org = $user['org'];
            }
        }
        $url = EYE_DOMAIN_HTTPS_PE . 'api/wechat/jump?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . '&is_new=' . IS_NEW_WX;
        // FD16 众佑
        $show_fd16_video_str = intval($user['org']['config']['show_fd16_video']) === 1 ? '&show_fd16_video=1' : '';
        $show_fd16_qrcode_str = intval($user['org']['config']['show_fd16_qrcode']) === 1 || $camera['work_mode'] == 4 ? '&show_fd16_qrcode=1' : '';
        $pay_str = '';
        if ($user['org_id'] == 1) {
            if (trim($user['config']['pay_config']) && is_numeric($user['config']['pay_config']) && bccomp($user['config']['pay_config'], 0.01, 2) >= 0) {
                $pay_str = "&pay_price={$user['config']['pay_config']}";
            }
        }
        $work_mode_str = '&work_mode=' . $camera['work_mode'] . '&is_zhongyou=1';
        $age_type = $user['org']['age_type'];
        $show_pay_page = $user['show_pay_page'] == -1 ? $user['org']['config']['show_pay_page'] : $user['show_pay_page'];
        if ($show_pay_page) {
            if (!$org && $user)
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
            if ($user['org_id'] && in_array($user['org_id'], TAIKANG_ORG_ID)) {
                $price = 598;
            }
            $url = EYE_DOMAIN_HTTPS_PE . 'landing/payment?sn=%s&en_openid=%s&pcode=%s&age_type=%s&is_fd16=1&org_id=%s&price=' . $price . '&origin_price=' . $origin_price;
            $url = sprintf($url, $sn, urlencode(\Air\Libs\Xcrypt::encrypt($openid)), urlencode($code), $age_type, $user['org_id']);
            if (CameraHandler::checkAgentNum($sn) && !($user['org_id'] == PA_ZY_ORG_ID && PA_SWITCH == 2 || $user['org_id'] == PA_APP_ORG_ID && PA_APP_SWITCH == 2)) {
                $url .= '&check_agent_num=1';
            }
        } else {
            $url = EYE_DOMAIN_HTTPS_PE . 'api/wechat/jump?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . '&noid=1&is_fd16=1&is_new=' . IS_NEW_WX . "&sn={$sn}&age_type={$age_type}{$pay_str}";
        }

        //微信推送点击链接添加一个参数substr6Sn，值为sn解密之后的后6位
        if (!empty($substr6Sn)) {
            $url .= "&substr6Sn={$substr6Sn}";
        }

        $url .= $show_fd16_video_str . $show_fd16_qrcode_str . $work_mode_str;
        if (isset($org['age_type'])) { // AK-1230：(0 || null)填写生日;1填写年龄
            $url .= ('&age_type=' . $org['age_type']);
        }
        $register_type = ($user['config']['register_type'] == -1 || !isset($user['config']['register_type'])) ? $user['org']['config']['register_type'] : $user['config']['register_type'];
        $camera['work_mode'] == 4 && $register_type = 1;
        if (isset($register_type)) {
            $url .= '&register_type=' . $register_type;
        }
        if ($user['org_id'] == PA_ZY_ORG_ID && PA_SWITCH == 1 || $user['org_id'] == PA_APP_ORG_ID && PA_APP_SWITCH == 1) {
            $effective_check_infos = CheckInfoUtil::getEffectiveCheckInfoByOpenid($openid, PA_ALL_ID);
            if (!$effective_check_infos) {
                $url .= '&jump_authorize=1';
            }
        } elseif ($show_pay_page == 2 || $user['org_id'] == PA_ZY_ORG_ID && PA_SWITCH == 2 || $user['org_id'] == PA_APP_ORG_ID && PA_APP_SWITCH == 2) {
            $url .= "&vcode=1&jump_authorize=1";
            // if (in_array($user['org_id'], TAIKANG_ZY_ORG_ID) || $user['org_id'] == PA_ZY_ORG_ID && PA_SWITCH == 2 || $user['org_id'] == PA_APP_ORG_ID && PA_APP_SWITCH == 2) {
            //     $url .= "&jump_authorize=1";
            // }
            if ($user['org_id'] == PA_ZY_ORG_ID || $user['org_id'] == PA_APP_ORG_ID) {
                $url .= "&get_customers=1";
            }
            $today_finished_num = RedisCount::getCount(date('ymd') . "_vcode_", $openid);
            // 【众佑】福利码模式，同一手机启动3次
            $throt = ENV == 'test' ? 3 : 3;
            if (in_array($user['org_id'], [40786, 40512])) {
                $throt = 1000;
            }
            if ($today_finished_num >= $throt) {
                $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), '您今天已经扫描多次，请明天继续。');
                return $info;
            }
        }
        $model = isset($camera['model'])?$camera['model']:'';
        $url .= "&model={$model}";

        // CP-670 SME近视防控
        if (!$org && isset($user['org'])) {
            $org = $user['org'];
        }
        if ($org) {
            if (isset($org['id']) && !strpos($url, 'org_id=')) {
                $url .= "&org_id=".$org['id'];
            }
            
            if (isset($org['config']['sme_config_is_sme_org']) && intval($org['config']['sme_config_is_sme_org']) > 0) {
                $url .= '&is_sme_org=' . $org['config']['sme_config_is_sme_org'];
                if (isset($org['config']['check_quantify_config']) && strpos($org['config']['check_quantify_config'], 'myopic_refraction') !== false) {
                    $url .= '&require_sphere=1';
                }
            }
        }

        if (defined('SWITCH_REGISTER_MINIPROGRAM') && ($user['org']['config']['rigister_miniprogram'] || SWITCH_REGISTER_MINIPROGRAM)) {
            // $msg = '<a data-miniprogram-appid=' . REGISTER_WX_APPID . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '" href="' . $url . '">填写信息开始检测>></a>';
            // $msg = '<a data-miniprogram-appid=' . REGISTER_WX_APPID . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '">填写信息开始检测>></a>';
            $wx_util = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
            if (ENV == 'test') {
                $msg = '<a data-miniprogram-appid=' . REGISTER_WX_APPID . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '">点击此处填写信息>></a>';
                $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
            } else {
                WechatUserCheck::sendRegisterMiniprogram($wx_util, ZY_REGISTER_TEMPLATE_ID, $openid, WXUtil::h5Url2Miniprogram($url), '众佑', REGISTER_WX_APPID);
                $info = '';
            }
        } else {
            $wx_util = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
            $url = $wx_util->getRedirectUrl($url, IS_NEW_WX);
            $msg = '<a href="' . $url . '">👉点击此处填写信息>></a>';
            $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
        }
        Logger::info('url=' . $url, 'zy_callback');
        return $info;
    }

    private function handleTextMsg()
    {
        $msg = '您的众佑健康评估报告正在生成中。报告生成后，您将收到一条微信通知。';
        $toUser = $this->post->FromUserName;
        $fromUser = $this->post->ToUserName;
        $time = $this->post->CreateTime;
        if (strpos('pre' . $this->post->EventKey, 'uuid_')) {
            $uuid = array_pop(explode('_', $this->post->EventKey));
            PatientCode::updateOpenidbyPcode($uuid, $this->openid);
            $pcode_item = PatientCode::getItemByPcode($uuid);
            $check_id = $pcode_item['check_id'];
        } elseif (strpos('pre' . $this->post->EventKey, 'vcode_')) {
            $vcode_info = explode('_', $this->post->EventKey);
            $vcode = array_pop($vcode_info);
            $org_id = array_pop($vcode_info);
            if (intval($org_id) == 0) {
                $org_id = 40786;
            }
            $vcode_item = VerificationCode::checkExist($vcode, $org_id);
            $uuid = $vcode_item['pcode'];
            if ($uuid) {
                PatientCode::updateOpenidbyPcode($uuid, $this->openid);
            }
            $pcode_item = PatientCode::getItemByPcode($uuid);
            $check_id = $pcode_item['check_id'];
            if (!$check_id) {
                $msg = '请先排队去检查，检查完成后2分钟再扫描此二维码获取健康评估报告。';
                $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
                return $info;
            }
        } elseif (strpos('pre' . $this->post->EventKey, 'qrscene_')) {
            $check_id = explode('_', $this->post->EventKey)[1];
        } else {
            $check_id = (int) $this->post->EventKey;
        }
        $cobj = new CheckInfo();
        $cobj->setCache(0);
        $cobj->setFromScript(1);
        $check_info = $cobj->getCheckDetail($check_id);
        $check_info = $check_info[0];
        list($can_push_report, $can_not_push_report_reasons) = CheckInfoUtil::canPushReport($check_info);
        if (!$can_push_report || $check_info['patient']['status'] == 0 ) {
            $lock = RedisLock::lock('can_not_push_report_zycallback_' . $check_info['check_id'], 60);
            if ($lock) {
                $content = '报告未生成，原因【' . implode(',', $can_not_push_report_reasons) . '】 上传时间：' . $check_info['created'] . ' 开始评估时间：' . $check_info['start_time'];
                Logger::info($content, 'can_not_push_report', ['check_id' => $check_info['check_id']]);
            }
            $db_status = 0;
            $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
        } else {
            $ret = WechatUserCheck::sendMsgByOpenId(['open_id' => $this->openid], $check_info, 1);
            $db_status = 1;
            $info = '';
        }
        if (!(ENV == 'production' && isset(self::$our_openids[$this->openid]) && !in_array($check_info['org_id'], [1, 5129]))) {
            $item = WechatUserCheck::addItem(['open_id' => $this->openid, 'check_id' => $check_id, 'status' => $db_status]);
        }
        $alarms = HandleAlarm::getAlarmByCheckIds($check_id);
        // 有警示单，新扫码的人
        if ($alarms && defined('IS_NEW_WX') && $item['open_id'] && !isset($item['updated'])) {
            $alarm = $alarms[$check_id];
            $witem = ['open_id' => $this->openid, 'check_id' => $check_id, 'new_wechat' => 3];
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
        if ($time < 8 || $time > 19) {
            return false;
        } else {
            return true;
        }
    }

    private function handleAutoResponse($click = 0)
    {
        $toUser = $this->post->FromUserName;
        $fromUser = $this->post->ToUserName;
        $time = $this->post->CreateTime;
        if (!$click) {
            $text = trim($this->post->Content);
        } else {
            $text = 'HELLO_AIRDOC';
        }
        $ab = FALSE;
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
            $wx_util = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
            $media_id = $wx_util->uploadImageMedia($filename);
            $info = sprintf($template, $toUser, $fromUser, $time,  $media_id);
            Logger::info($info, 'wechat_sevice_qr');
        }
        return $info;
    }

    private function handleFD16($eventKey)
    {
        $sn = array_pop(explode('_', $eventKey));
        $prefix = '8996';
        return $this->getPatientInfoLink($prefix, $sn);
    }
}
