<?php

namespace Air\Modules\WeChat;

use Air\Libs\Base\Utilities;
use \Phplib\Tools\Logger;
use \Air\Package\Wechat\WechatMsgTemplate;
use \Air\Libs\Xcrypt;
use Air\Package\Bisheng\BishengUtil;
use Air\Package\Cache\RedisCache;
use \Air\Package\User\PatientCode;
use \Air\Package\Checklist\CheckInfo;
use Air\Package\Checklist\CheckInfoUtil;
use Air\Package\Checklist\CheckLog;
use Air\Package\Checklist\Helper\RedisCount;
use Air\Package\Checklist\Helper\RedisLock;
use \Air\Package\Fd16\CameraHandler;
use \Air\Package\Fd16\DeviceVersion;
use Air\Package\Thirdparty\ThirdHandler;
use Air\Package\Thirdparty\ZhongyingHandler;
use \Air\Package\User\User;
use Air\Package\Wechat\WechatMedia;
use \Air\Package\Wechat\WechatUserCheck;
use \Air\Package\Wechat\WXUtil;
use Phplib\Tools\CommonFun;

class ICVDCallback extends \Air\Libs\Controller
{
    const MEDIA = [
        'production' => [
            'product' => '_qjBmJCj8U6kFq91rK7zsA-VPb-JJ7hEx7QPDdQ4VsQ',
            'service' => '_qjBmJCj8U6kFq91rK7zsEB-M8H2Z5QaJitlwbQ7zuU',
        ],
        'test' => [
            'product' => 'R7i6BPiBnFlVTVSOGSdEMg_dNy0QBqNwyAbseRF-RfA',
            'service' => 'R7i6BPiBnFlVTVSOGSdEMv_QtesYeORDBKaUsDpI_1g',
        ]
    ];
    private $post;
    public function run()
    {
        $token = ICVD_WX_TOKEN;
        $request = $this->request->REQUEST;
        $timestamp = $request['timestamp'];
        $echostr   = isset($request['echostr']) ? $request['echostr'] : '';
        $signature = $request['signature'];
        $nonce     = $request['nonce'];
        $openid    = $request['openid'];
        $array = array($nonce, $timestamp, $token);
        sort($array, SORT_STRING);
        $this->openid = $openid;
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1(implode($array));
        if ($str == $signature) {
            // 微信开发者设置验证代码
            if ($echostr) {
                echo $echostr;
                exit;
            }
            $postArr = file_get_contents("php://input");
            $this->post = $postObj = simplexml_load_string($postArr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $postObj->EventKey = (string) $postObj->EventKey;
            Logger::error($postObj, 'icvd_wechat_callback');
            // check if it's event message
            $fromUser = (string) $postObj->FromUserName; // user openid
            $toUser = (string) $postObj->ToUserName; // wechat account
            $time = (string) $postObj->CreateTime;
            $str = '';
            if (strtolower($postObj->MsgType) == 'event') {
                $eventKey = (string) $postObj->EventKey;
                if (strtolower($postObj->Event) == 'subscribe') {
                    // 函数内部会判断是否转发到欢瞳
                    $this->forward2Huantong();
                    if ($eventKey) {
                        $sn = str_replace('qrscene_', '', $eventKey);
                    } else {
                        $sn = "";
                    }
                    $str = $this->handleEvent($eventKey, $sn, $fromUser, $toUser);
                    echo $str;
                    exit;
                } else if ($postObj->Event == 'SCAN') {
                    // 函数内部会判断是否转发到欢瞳
                    $this->forward2Huantong();
                    $sn = $eventKey;
                    $str = $this->handleEvent($eventKey, $sn, $fromUser, $toUser);
                    echo $str;
                    exit;
                } elseif ($postObj->Event == 'CLICK') {
                    $eventKey = $postObj->EventKey;
                    if ($eventKey == 'AIRDOC_PRODUCT') {
                        // $info = $this->handleAutoResponse(1);
                        $info = sprintf(WechatMsgTemplate::MSG_COMMON_IMAGE, $fromUser, $toUser, $time, self::MEDIA[ENV]['product']);
                    } else if ($eventKey == 'REPORT_CUSTOMER_SERVICE') {
                        $info = sprintf(WechatMsgTemplate::MSG_COMMON_IMAGE, $fromUser, $toUser, $time, self::MEDIA[ENV]['service']);
                    } else if ($eventKey == 'ONLINE_SERVICE') {
                        $msg_online_service = "您好，您可以直接在聊天框内输入您的问题！\n\n客服工作时间：08:00--20:00";
                        $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, $time, $msg_online_service);
                    } else {
                        $info = "";
                    }
                    echo $info;
                    exit;
                } else if ($postObj->Event == 'TEMPLATESENDJOBFINISH') {
                    $msgType = $postObj->MsgType;
                    $event = $postObj->Event;
                    $status = $postObj->Status;
                    $msgId = $postObj->MsgID;
                    $template = WechatMsgTemplate::MSG_TEMPLATE_REPLY;
                    $info = sprintf($template, $fromUser, $toUser, $time, $msgType, $event, $msgId, $status);
                    echo $info;
                    exit;
                } else {
                    echo '';
                    exit;
                }
            } elseif (strtolower($postObj->MsgType) == 'text') {
                //$info = $this->handleAutoResponse();
                //echo $info;
                echo '';
                exit;
            } else {
                echo '';
                exit;
            }
        }
    }

    // 转发到欢瞳服务
    private function forward2Huantong()
    {
        if (!strpos($this->post->EventKey, '_huantong_')) {
            return '';
        }
        $param = (array) $this->post;
        $param['from'] = 'pangu';
        $url = HUANTONG_API . "api/wechat/callback";
        $info = CommonFun::callOpenAPI($url, $param, [], ['need_decode' => 0, 'is_post' => 1, 'is_json' => 1]);
        echo $info;
        exit();
    }
    private function handleEvent($eventKey, $sn, $fromUser, $toUser, $subscriber = true)
    {
        if (strpos($eventKey, 'zhongyi')) {
            //$wx_util = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
            $url = 'https://foreign-report-prd.zhiyuntcm.com/login';
            $msg = '👉<a href="' . $url . '">点击查看您的中医体质报告>></a>';
            $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
            // $str = '';
            // WechatUserCheck::sendRegisterMiniprogram($wx_util, ICVD_REGISTER_TEMPLATE_ID, $fromUser, $url, '鹰瞳健康');
        } elseif (strlen($sn) > 32 && strpos($eventKey, 'woman_report')) {
            //女性报告
            $sn = substr($sn, 0, 32);
            $type = 11; // 存在patinet_code 表中 代表女性报告
            $str = $this->handleFD16($sn, $fromUser, $toUser, $subscriber, $type);
        } elseif (strlen($sn) > 32 && strpos($eventKey, 'annuity_report')) {
            //年金版报告
            $sn = substr($sn, 0, 32);
            $type = 10; // 存在patinet_code 表中 代表年金版报告
            $str = $this->handleFD16($sn, $fromUser, $toUser, $subscriber, $type);
        } elseif (strlen($sn) == 32) {
            $str = $this->handleFD16($sn, $fromUser, $toUser, $subscriber);
        } else if (strpos('pre' . $eventKey, 'ICVD_BIG_CAMERA_REG_BEFORE')) {
            $str = $this->handleRegisterFirstForBigCamera($fromUser, $toUser, $eventKey);
        } else if (strpos('pre' . $eventKey, 'ICVD_BIG_CAMERA')) {
            $str =  $this->handleBigCamera($fromUser, $toUser, $eventKey);
        } else if (strpos('pre' . $eventKey, 'INTELLIGENT_VOICE')) {
            $str =  $this->handleVoice($fromUser, $toUser, $eventKey);
        } else if ($sn) {
            $str = $this->handleTextMsg($sn);
        }

        return $str;
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
        $ab = TRUE;
        if (strpos('pre' . $text, 'aaaa')) {
            $ab = TRUE;
        } elseif (strpos('pre' . $text, 'bbbb')) {
            $ab = FALSE;
        }
        $msgType = 'text';
        if ($ab) {
            if ($this->isWorkTime()) {
                Logger::info("[{$toUser}] [$text] [aba_work]", 'wechat_sevice_qr');
                $content = "您好，这里是人工客服，请问有什么可以帮您？";
            } else {
                Logger::info("[{$toUser}] [$text] [aba_out]", 'wechat_sevice_qr');
                $content = "您好，全国统一客服电话：400-100-3999，我们的工作时间为周一至周日的8点至19点，现在为非工作时间，请您留言或留下联系方式，我们会在上班后第一时间联系您！";
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
            $media_id = RedisCache::getCache('wechat_icvd_media');
            if (!$media_id) {
                $wx_util = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
                $media_id = $wx_util->uploadImageMedia($filename);
                RedisCache::setCache('wechat_icvd_media', $media_id, '', 86400);
            }
            $info = sprintf($template, $toUser, $fromUser, $time,  $media_id);
            Logger::info($info, 'wechat_sevice_qr');
        }
        return $info;
    }

    private function handleRegisterFirstForBigCamera($fromUser, $toUser, $eventKey)
    {
        $openid = $fromUser;
        $prefix = ICVD_PCODE_PREFIX;
        $type = 0;
        $not_push = 0;
        // generate pcode
        $new_wechat = 2;
        $hxt_plus_agent_str = '';
        if (strpos('pre' . $eventKey, 'ICVD_BIG_CAMERA_REG_BEFORE_EYE')) {
            $prefix = '8996';
            $hxt_plus_agent_str = '&hxt_plus_agent=1';
        }
        $code = PatientCode::getFreeCodeByOpenid($openid, $prefix, $type, $new_wechat, true);
        if (!$code) {
            list($id, $code) = PatientCode::initCode($openid, $prefix, $not_push, $type, $new_wechat);
        }

        $check_log_remark = ['data' => ['event' => (string)$this->post->Event, 'event_key' => (string)$this->post->EventKey, 'sn' => $plain_sn ?? '']];
        if (strtolower($this->post->Event) == 'subscribe') {
            $check_log_remark['lang'] = "扫码关注了公众号";
        } elseif ($this->post->Event == 'SCAN') {
            $check_log_remark['lang'] = "扫码进入了公众号";
        } elseif ($this->post->Event == 'CLICK') {
            $check_log_remark['lang'] = "点击了自定义菜单";
        }
        CheckLog::addLogInfo(0, 'wechat_icvd_callback_event', $check_log_remark, 0, '', $code);

        $url = EYE_DOMAIN_HTTPS_PE . 'landing/payment?en_openid=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&is_yingtong=1&pcode=' . urlencode(Xcrypt::encrypt($code)) . $hxt_plus_agent_str;
        if (strpos('pre' . $eventKey, 'ICVD_BIG_CAMERA_REG_BEFORE_EYE') && !strpos('pre' . $eventKey, 'ICVD_BIG_CAMERA_REG_BEFORE_EYE_PAY')) {
            $url = EYE_DOMAIN_HTTPS_PE . 'userinfo/set?en_openid=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&is_yingtong=1&pcode=' . urlencode(Xcrypt::encrypt($code)) . $hxt_plus_agent_str;
        }
        Logger::info('big_camera_url=' . $url, 'icvd_callback');
        $wx_util = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
        if (defined('SWITCH_REGISTER_MINIPROGRAM') && SWITCH_REGISTER_MINIPROGRAM) {
            // $msg = '<a data-miniprogram-appid=' . REGISTER_WX_APPID . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '" href="' . $url . '">填写信息开始检测>></a>';
            // $msg = '<a data-miniprogram-appid=' . REGISTER_WX_APPID . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '">填写信息开始检测>></a>';
            WechatUserCheck::sendRegisterMiniprogram($wx_util, ICVD_REGISTER_TEMPLATE_ID, $openid, WXUtil::h5Url2Miniprogram($url), '鹰瞳健康', REGISTER_WX_APPID);
            $str = '';
        } else {
            // $msg = '<a href="' . $url . '">👉点击此处填写信息>></a>';
            // $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
            WechatUserCheck::sendRegisterMiniprogram($wx_util, ICVD_REGISTER_TEMPLATE_ID, $openid, $url, '鹰瞳健康');
            $str = '';
        }
        return $str;
    }

    private function handleFD16($sn, $fromUser, $toUser, $subscriber, $type = 0)
    {
        Logger::info(['handleFD16', $sn, $fromUser, $toUser, $subscriber], 'zhongying_handler');
        $openid = $fromUser;
        $prefix = ICVD_PCODE_PREFIX;
        $not_push = 0;
        // generate pcode
        $new_wechat = 2;
        if ($sn) {
            $bisheng_camera = CameraHandler::getCameraBySNOrMd5($sn);
            if ($bisheng_camera['sn']) {
                $BishengUtil = new BishengUtil();
                $bisheng_config = $BishengUtil->getConfigByDevice($bisheng_camera['sn']);
            }
        }
        $camera = CameraHandler::getCameraBySN($sn);
        if ($camera['status']) {
            $msg = '您所使用的健康扫描仪已经停止工作，请您联系现场的工作人员或拨打400-100-3999';
            $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
            return $str;
        }
        $camera_env = DeviceVersion::getCameraEnv($camera['sn']);
        $camera_env = $camera_env ? 'test' : 'production';
        if ($camera_env !== ENV) {
            $msg = '您所使用的健康扫描仪环境配置错误，请您联系现场的工作人员或拨打400-100-3999';
            $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
            return $str;
        }
        $fee_status = CameraHandler::getFeeStatus($camera['sn']);
        if ($fee_status == 1) {
            $msg = '您所使用的健康扫描仪还未充值，请您联系现场的工作人员';
            $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
            return $str;
        } else if ($fee_status == 2) {
            $msg = '您所使用的健康扫描仪使用已到期，请您联系现场的工作人员';
            $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
            return $str;
        }
        $user_id = $camera['user_id'];
        $user_obj = new User();
        $user = $user_obj->getUserById($user_id);
        //使用第三方公众号启动相机
        if (WXUtil::thirdQrMatchReport($user['org_id'], 'yingtong')) {
            $msg = '亲，您扫错二维码了，请联系工作人员获取正确的二维码。';
            $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
            return $str;
        }
        if (in_array($user['org_id'], PA_HFL_ORG_ID)) {
            $media_id = [
                'production' => '_qjBmJCj8U6kFq91rK7zsBL03BFP4FGy0bFvwYJg_htDNa1mxTzOgOGQfJlHdwt8',
                'test' => 'R7i6BPiBnFlVTVSOGSdEMkslj8VO164Ii2g-O6yMiKA6Qa8sFzcte-Iu3EOG8M2A',
            ][ENV] ?? '';
            if ($media_id) {
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_IMAGE, $fromUser, $toUser, time(), $media_id);
                return $str;
            }
        }
        if ($user['org']['customer_id'] != 5 || in_array($user['org']['new_template'], [20])) {
            $prefix = '8996';
            $qr_match_report['error_code'] = true;
        } else {
            // 判断用户扫描与机构报告是否一致
            $qr_match_report = WXUtil::qrMatchReport($user['org']['customer_id'], 'yingtong');
        }
        if (!$qr_match_report['error_code']) {
            $auth_user = CameraHandler::getAuthUser($camera['sn']);
            $auth_name = $auth_user ? $auth_user['name'] : '-';
            $auth_phone = $auth_user ? $auth_user['phone'] : '-';
            $msg = '亲，您扫错二维码了，请联系工作人员获取正确的二维码！';
            if ($user['org_id'] == SKB_ORG_ID) {
                $msg = '亲，本产品的测试阶段已结束，无法体验了哦~';
            } else {
                $media_id = WechatMedia::SCAN_QRCODE_TIPS[ENV]['yingtong'][$qr_match_report['report_type']];
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_IMAGE, $fromUser, $toUser, time(), $media_id);
                return $str;
            }
            Utilities::DDMonitor("P3-pangu-【OP】SN: {$camera['sn']} 是机构名称：{$user['org']['name']}, 账号：{$user['name']}，授权手机号：{$auth_phone}, 联系人：{$auth_name}, 用户扫错二维码（扫了{$qr_match_report['qr_report_name']}二维码），请跟进。", 'bigop', WXUtil::OP_PHONES);
            $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
            return $str;
        }
        if (in_array($user['org_id'], ThirdHandler::ORG_IDS['taiping'])) {
            // $auth_user = CameraHandler::getAuthUser($camera['sn'], $user);
            // $auth_phone = $auth_user ? $auth_user['phone'] : '-';
            // $auth_name = $auth_user ? $auth_user['name'] : '-';
            // $msg = '亲，您扫错二维码了，请联系工作人员获取正确的二维码！';
            // Utilities::DDMonitor("P3-pangu-【OP】SN: {$camera['sn']} 是太平人寿项目的设备, 账号：{$user['name']}，授权手机号：{$auth_phone}, 联系人：{$auth_name}, 用户扫错二维码（扫了鹰瞳健康二维码），请跟进。", 'bigop', WXUtil::OP_PHONES);
            // $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
            Logger::info($camera['sn'], 'taiping_qrcode_info');
            $url = ThirdHandler::getTaipingQRUrl($camera['sn']);
            // 判断设备类型
            $model = isset($camera['model']) ? $camera['model'] : '';
            $url .= "&model={$model}";
            $msg = '👉<a href="' . $url . '">请点击开始检测（太平用户专属）</a>';
            $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
            return $str;
        }
        if ($user && ($user['config']['alarm_recycle'] == 1 ||
            $user['org']['config']['alarm_recycle'] == -1 && $user['org']['config']['alarm_recycle'] == 1)) {
            $expire_info = CameraHandler::getExpireInfo($camera['sn']);
            if ($expire_info && date('Y-m-d') > $expire_info['expire_time']) {
                $msg = '您所使用的健康扫描仪已到期停止工作，请您联系现场的工作人员或拨打400-100-3999';
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
                return $str;
            }
        }
        $hxt_plus_agent = intval($user['org']['config']['hxt_plus_agent']);
        $show_fd16_video_str = intval($user['org']['config']['show_fd16_video']) === 1 ? '&show_fd16_video=1' : '';
        $show_fd16_qrcode_str = intval($user['org']['config']['show_fd16_qrcode']) === 1 || $camera['work_mode'] == 4 ? '&show_fd16_qrcode=1' : '';
        $hxt_plus_agent_str = intval($user['org']['config']['hxt_plus_agent']) === 1 ? '&hxt_plus_agent=1' : '';
        $industry_str = '&industry=1';
        if ($hxt_plus_agent === 1) {
            $prefix = '8996';
        }
        $code = PatientCode::getFreeCodeByOpenid($openid, $prefix, $type, $new_wechat, true);
        if (!$code) {
            list($id, $code) = PatientCode::initCode($openid, $prefix, $not_push, $type, $new_wechat);
        }
        Logger::info("code：$code", 'icvd_callback');

        $check_log_remark = ['data' => ['event' => (string)$this->post->Event, 'event_key' => (string)$this->post->EventKey, 'sn' => $camera['sn'] ?? '']];
        if (strtolower($this->post->Event) == 'subscribe') {
            $check_log_remark['lang'] = "扫码关注了公众号";
        } elseif ($this->post->Event == 'SCAN') {
            $check_log_remark['lang'] = "扫码进入了公众号";
        } elseif ($this->post->Event == 'CLICK') {
            $check_log_remark['lang'] = "点击了自定义菜单";
        }
        CheckLog::addLogInfo(0, 'wechat_icvd_callback_event', $check_log_remark, 0, '', $code);

        $register_type = ($user['config']['register_type'] == -1 || !isset($user['config']['register_type'])) ? $user['org']['config']['register_type'] : $user['config']['register_type'];
        $camera['work_mode'] == 4 && $register_type = 1;
        if (!empty($bisheng_config)) {
            $show_pay_page = $bisheng_config['show_pay_page'];
        } else {
            $show_pay_page = $user['show_pay_page'] == -1 ? $user['org']['config']['show_pay_page'] : $user['show_pay_page'];
        }
        $org_id = $user['org_id'];
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
        if (in_array($org_id, TAIKANG_ORG_ID)) {
            $price = 598;
        }
        if ($register_type && $show_pay_page) {
            $url = EYE_DOMAIN_HTTPS_PE . 'landing/payment?sn=%s&en_openid=%s&pcode=%s&t=%s&age_type=%s&is_fd16=1&is_yingtong=1&register_type=1&price=' . $price . '&origin_price=' . $origin_price . '&org_id=' . $org_id;
        } elseif ($register_type) {
            $url = EYE_DOMAIN_HTTPS_PE . 'fd16/fulluserinfo/set?sn=%s&en_openid=%s&pcode=%s&t=%s&age_type=%s&is_fd16=1&is_yingtong=1';
        } elseif ($show_pay_page) {
            $url = EYE_DOMAIN_HTTPS_PE . 'landing/payment?sn=%s&openid=%s&pcode=%s&t=%s&is_fd16=1&is_yingtong=1&register_type=0&price=' . $price . '&origin_price=' . $origin_price;
        } else {
            $url = EYE_DOMAIN_HTTPS_PE . 'fd16/userinfo/set?sn=%s&openid=%s&pcode=%s&t=%s&is_fd16=1&is_yingtong=1';
        }
        $vcode_str = '';
        $today_finished_num = 0;
        if ($show_pay_page == 2 || in_array($user['org_id'], VCODE_ORG_ID)) {
            $vcode_str = "&vcode=1";
            if (in_array($user['org_id'], VCODE_ORG_ID)) {
                $vcode_str .= "&jump_authorize=1";
            }
            $today_finished_num = RedisCount::getCount(date('ymd') . "_vcode_", $openid);
            // 【鹰瞳健康】福利码模式，同一手机启动3次
            $throt = ENV == 'test' ? 50 : 3;
            if ($today_finished_num >= $throt) {
                $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), '您今天已经扫描多次，请明天继续。');
                return $info;
            }
        }
        $url .= $show_fd16_video_str . $show_fd16_qrcode_str . $hxt_plus_agent_str . $vcode_str . $industry_str;
        if (CameraHandler::checkAgentNum($sn)) {
            $url .= '&check_agent_num=1';
        }
        $url = sprintf($url, $sn, urlencode(Xcrypt::encrypt($openid)), $code, time(), $user['org']['age_type']);
        //微信推送点击链接添加一个参数substr6Sn，值为sn解密之后的后6位
        if (isset($camera['sn']) && !empty($camera['sn'])) {
            $substr6Sn = substr($camera['sn'], -6);
            $url .= "&substr6Sn={$substr6Sn}";
        }
        // 中英人寿需要修改业务员工号处的文案
        if (in_array($org_id, ZhongyingHandler::ORG_ID[ENV])) {
            $url .= "&is_zhongying=1";
        }
        // 判断设备类型
        $model = isset($camera['model']) ? $camera['model'] : '';
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

        $wx_util = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
        if (defined('SWITCH_REGISTER_MINIPROGRAM') && ($user['org']['config']['rigister_miniprogram'] || SWITCH_REGISTER_MINIPROGRAM) && $subscriber) {
            if (ENV == 'test') {
                $msg = '<a data-miniprogram-appid=' . REGISTER_WX_APPID . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '">点击此处填写信息>></a>';
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
            } else {
                WechatUserCheck::sendRegisterMiniprogram($wx_util, ICVD_REGISTER_TEMPLATE_ID, $openid, WXUtil::h5Url2Miniprogram($url), '鹰瞳健康', REGISTER_WX_APPID);
                $str = '';
            }
        } else {
            $msg = '👉<a href="' . $url . '">填写信息开始检测>></a>';
            // $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
            $str = '';
            WechatUserCheck::sendRegisterMiniprogram($wx_util, ICVD_REGISTER_TEMPLATE_ID, $openid, $url, '鹰瞳健康');
        }
        Logger::info('fd16_url=' . $url, 'icvd_callback');
        return $str;
    }

    private function handleBigCamera($fromUser, $toUser, $event_key)
    {
        $openid = $fromUser;
        $prefix = ICVD_PCODE_PREFIX;
        $hxt_plus_agent_str = '';
        if (strpos('pre' . $event_key, 'ICVD_BIG_CAMERA_EYE')) {
            $prefix = '8996';
            $hxt_plus_agent_str = '&hxt_plus_agent=1';
        }
        $type = 0;
        $not_push = 0;
        // generate pcode
        $new_wechat = 2;
        $code = PatientCode::getFreeCodeByOpenid($openid, $prefix, $type, $new_wechat, true);
        if (!$code) {
            list($id, $code) = PatientCode::initCode($openid, $prefix, $not_push, $type, $new_wechat);
        }

        $check_log_remark = ['data' => ['event' => (string)$this->post->Event, 'event_key' => (string)$this->post->EventKey, 'sn' => $camera['sn'] ?? '']];
        if (strtolower($this->post->Event) == 'subscribe') {
            $check_log_remark['lang'] = "扫码关注了公众号";
        } elseif ($this->post->Event == 'SCAN') {
            $check_log_remark['lang'] = "扫码进入了公众号";
        } elseif ($this->post->Event == 'CLICK') {
            $check_log_remark['lang'] = "点击了自定义菜单";
        }
        CheckLog::addLogInfo(0, 'wechat_icvd_callback_event', $check_log_remark, 0, '', $code);

        $url = EYE_DOMAIN_HTTPS_PE . 'icvd/register?en_openid=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&is_yingtong=1&pcode=' . urlencode($code) . $hxt_plus_agent_str;
        Logger::info('big_camera_url=' . $url, 'icvd_callback');
        $wx_util = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
        if (defined('SWITCH_REGISTER_MINIPROGRAM') && SWITCH_REGISTER_MINIPROGRAM) {
            // $msg = '<a data-miniprogram-appid=' . REGISTER_WX_APPID . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '" href="' . $url . '">填写信息开始检测>></a>';
            // $msg = '<a data-miniprogram-appid=' . REGISTER_WX_APPID . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '">填写信息开始检测>></a>';
            WechatUserCheck::sendRegisterMiniprogram($wx_util, ICVD_REGISTER_TEMPLATE_ID, $openid, WXUtil::h5Url2Miniprogram($url), '鹰瞳健康', REGISTER_WX_APPID);
            $str = '';
        } else {
            // $msg = '👉<a href="' . $url . '">填写信息开始检测>></a>';
            $str = '';
            WechatUserCheck::sendRegisterMiniprogram($wx_util, ICVD_REGISTER_TEMPLATE_ID, $openid, WXUtil::h5Url2Miniprogram($url), '鹰瞳健康', REGISTER_WX_APPID);
            return '';
        }
        // $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
        return $str;
    }

    private function handleTextMsg($check_id)
    {
        $msg = '您的报告正在生成中。报告生成后，您将收到一条微信通知。';
        $toUser = $this->post->FromUserName;
        $fromUser = $this->post->ToUserName;
        $time = $this->post->CreateTime;
        if (!is_numeric($check_id)) {
            return '';
        }
        $cobj = new CheckInfo();
        $cobj->setCache(0);
        $cobj->setFromScript(1);
        $check_info = $cobj->getCheckDetail($check_id);
        $check_info = $check_info[0];
        list($can_push_report, $can_not_push_report_reasons) = CheckInfoUtil::canPushReport($check_info);
        if (!$can_push_report || $check_info['patient']['status'] == 0) {
            $lock = RedisLock::lock('can_not_push_report_icvdcallback_' . $check_info['check_id'], 60);
            if ($lock) {
                $content = '报告未生成，原因【' . implode(',', $can_not_push_report_reasons) . '】 上传时间：' . $check_info['created'] . ' 开始评估时间：' . $check_info['start_time'];
                Logger::info($content, 'can_not_push_report', ['check_id' => $check_info['check_id']]);
            }
            $template = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                </xml>";
            $info = sprintf($template, $toUser, $fromUser, $time, 'text', $msg);
        } else {
            $ret = WechatUserCheck::sendICVDMsgByOpenId(['check_id' => $check_id, 'open_id' => $this->openid], $check_info, 1);
            $info = '';
        }
        //$item = WechatUserCheck::addItem(['open_id' => $this->openid, 'check_id' => $check_id, 'status' => $db_status]);
        return $info;
    }
    public function handleVoice($fromUser, $toUser, $eventKey)
    {
        $check_id = RedisCache::getCache($fromUser, "VOICE");
        $pcode = PatientCode::getItemsByOpenid($fromUser);
        foreach ($pcode as $k => $v) {
            if ($v['check_id'] && $check_id == $v['check_id']) {
                echo '';
                die;
            }
            if ($v['check_id']) {
                break;
            }
        }
        $msg = "您的报告正在生成中，请耐心等待~\n当您查看报告后会为您分配专属智能报告解读服务~";
        return sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $fromUser, $toUser, time(), $msg);
    }
}
