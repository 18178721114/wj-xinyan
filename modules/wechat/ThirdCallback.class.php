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
use Air\Package\Checklist\CheckInfoUtil;
use Air\Package\Checklist\CheckLog;
use \Air\Package\Fd16\CameraHandler;
use \Air\Package\Fd16\DeviceVersion;
use \Air\Package\Smb\SnPcode;
use Air\Package\Thirdparty\ThirdHandler;
use \Air\Package\User\Organizer;
use Air\Package\User\User;
use Air\Package\User\VerificationCode;
use Air\Package\Wechat\WechatMedia;
use Air\Package\Wechat\WechatThird;
use \Phplib\Tools\Logger;

class ThirdCallback extends \Air\Libs\Controller
{
    const SWITCHS = 1;
    /**
     * 1. 绑定筛查登记小程序（需要管理员操作）
     * 2. 模版消息：注意行业（IT科技/IT软件与服务，医疗护理/保健与卫生）如行业不同，需要寻找替代模板
     * 3. 需要知道AppSecret，否则需要重置（需要管理员，登录密码）
     * 4. 添加 IP白名单、业务域名、JS接口安全域名、网页授权域名（上传文件）
     * 5. 数据库wechat_config 和 wechat_config_template
     * 6. 服务器配置并启用
     * 7. 自定义菜单，若原来有网页端设置的菜单。需要重新用接口设置一遍。
     */
    public function run()
    {
        $token = TZJ_WX_TOKEN;
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
            $fromUser_prefix = substr($toUser, 0, 5);
            $wechat_config_data['prefix'] =  $fromUser_prefix;
            $this->wechat_config = WechatThird::getWechatConfig($wechat_config_data);
            if (!$this->wechat_config) {
                Logger::error("wechat_config_error", 'wechat_callback');
                return '';
            }
            $appid = $this->wechat_config['appid'];
            $third_public_name = $this->wechat_config['name'];
            $time = (string)$postObj->CreateTime;
            $eventKey = (string)$postObj->EventKey;
            Logger::error("wechat_event_msg:{$openid};postObj:" . json_encode($postObj), 'wechat_callback');
            //判断该数据包是否是订阅的事件推送
            if (strtolower($postObj->MsgType) == 'event') {
                //如果是关注 subscribe 事件
                if (strtolower($postObj->Event) == 'subscribe') {
                    //回复用户消息(纯文本格式)
                    if (strpos('pre' . $eventKey, 'IVAK_GETCODE_HOSPITAL_ORG')) {
                        $org_id = explode('channel_IVAK_GETCODE_HOSPITAL_ORG_', $eventKey)[1];
                        $prefix = $this->getCodePrefix('IVAK_GETCODE_HOSPITAL_ORG', $org_id);
                        $info = $this->getPatientInfoLink($prefix, '', $org_id);
                    } elseif (strpos('pre' . $eventKey, 'THIRD_')) {
                        $info = $this->handleFD16($eventKey);
                    } elseif (!empty($eventKey)) {
                        $info = $this->handleTextMsg();
                    } else {

                        if ($appid == 'wxd29a60c53cc2e81d') {

                            $msg = "感谢您关注北京和润诊所微信公众号！\r\n\r\n这里有丰富的医疗科普知识、医院各科室动态，以及最新的医疗产品优惠信息。请您持续关注。\r\n\r\n诊所地址：北京市东城区东单北大街3>号三层\r\n\r\n如有急需，请直接拨打服务电话：010-65598872 \r\n\r\n点击 <a href=".'"https://weixin.kbjcc.cc/html/pm/weixin_auth.html?clinicCode=bjhr001&pageCode=ptRegister&authType=1"'. ">【会员中心】</a>，建立您的个人专属健康档案";
                        } else {
                            $msg = '欢迎关注' . $third_public_name . '。如果您对您的报告有任何问题，回复消息可以咨询我们的客服人员。';
                        }

                        $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
                    }
                } elseif ($postObj->Event == 'SCAN') {
                    if (strpos('pre' . $eventKey, 'IVAK_GETCODE_HOSPITAL_ORG')) {
                        $arr = explode('_', $eventKey);
                        $org_id = array_pop($arr);
                        $prefix = $this->getCodePrefix('IVAK_GETCODE_HOSPITAL_ORG', $org_id);
                        $info = $this->getPatientInfoLink($prefix, '', $org_id);
                    } elseif (strpos('pre' . $eventKey, 'THIRD_')) {
                        $info = $this->handleFD16($eventKey);
                    } elseif (!empty($eventKey)) {
                        $info = $this->handleTextMsg();
                    }
                } elseif ($postObj->Event == 'TEMPLATESENDJOBFINISH') {
                    $info = $this->handleTEMPLATESENDJOBFINISH();
                } elseif ($postObj->Event == 'CLICK') {
                }
            } elseif (strtolower($postObj->MsgType) == 'text') {
                $info = 'success';

                if ($appid == 'wxd29a60c53cc2e81d') {
                    $msg = '您好，如果您有问题咨询，请直接拨打服务电话：010-65598872 ';
                    $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
                } elseif (self::SWITCHS) {
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
    private function getPatientInfoLink($prefix = '8989', $sn = '', $org_id = 0)
    {
        $plain_sn = "";
        $toUser = $this->post->FromUserName;
        $fromUser = $this->post->ToUserName;
        $time = $this->post->CreateTime;
        $eventKey = (string) $this->post->EventKey;
        $openid = (string) $toUser;
        $product = $this->wechat_config['product_type'];
        $type = 0;
        $org = $user = [];
        if ($sn) {
            $camera = CameraHandler::getCameraBySN($sn);
            $auth_user = CameraHandler::getAuthUser($camera['sn']);
            $auth_phone = $auth_user ? $auth_user['phone'] : '-';
            $auth_name = $auth_user ? $auth_user['name'] : '-';
            if ($camera['status']) {
                $msg = '您所使用的健康扫描仪已经停止工作，请您联系现场的工作人员或拨打400-100-3999';
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                Utilities::DDMonitor("P3-pangu-【OP】SN: {$camera['sn']}  是机构名称：{$user['org']['name']}, 账号：{$user['name']},授权手机号：{$auth_phone}，联系人：{$auth_name},  您所使用的健康扫描仪已经停止工作，请跟进", 'bigop', WXUtil::OP_PHONES);

                return $str;
            }
            $camera_env = DeviceVersion::getCameraEnv($camera['sn']);
            $camera_env = $camera_env ? 'test' : 'production';
            if ($camera_env !== ENV) {
                $msg = '您所使用的健康扫描仪环境配置错误，请您联系现场的工作人员或拨打400-100-3999';
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                Utilities::DDMonitor("P3-pangu-【OP】SN: {$camera['sn']}  是机构名称：{$user['org']['name']}, 账号：{$user['name']},授权手机号：{$auth_phone}，联系人：{$auth_name},  您所使用的健康扫描仪环境配置错误，请跟进", 'bigop', WXUtil::OP_PHONES);
                return $str;
            }
            $plain_sn = $camera['sn'];
            $user_id = $camera['user_id'];
            if (!$user_id) {
                $msg = '👉相机未绑定账号，请先绑定到账号！';
                $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
                Utilities::DDMonitor("P3-pangu-【OP】SN: {$camera['sn']}  是机构名称：{$user['org']['name']}, 账号：{$user['name']}, 授权手机号：{$auth_phone}，联系人：{$auth_name}, 相机未绑定账号，请跟进。", 'bigop', WXUtil::OP_PHONES);
                return $str;
            }
            $u_obj = new User();
            $user = $u_obj->getUserById($user_id);
            $package_type = $user['org']['type'];
            $prefix = PatientCode::$package_prefix[$package_type] ?? $prefix;
            // 判断用户扫描与机构报告是否一致
            if ($product == 1) {
                $product_name = "huixintong";
            } elseif ($product == 2) {
                $product_name = "yingtong";
            } elseif ($product == 3) {
                $product_name = "zhongyou";
            } elseif ($product == 4) {
                $product_name = "tizhijian";
            }
            // $qr_match_report = WXUtil::qrMatchReport($user['org']['customer_id'], $product_name);
            // if (!$qr_match_report['error_code']) {
            //     // $msg = '亲，您扫错二维码了，请联系工作人员获取正确的二维码！';
            //     Utilities::DDMonitor("P3-pangu-【OP】SN: {$camera['sn']}  是机构名称：{$user['org']['name']}, 账号：{$user['name']}，授权手机号：{$auth_phone}，联系人：{$auth_name}, 用户扫错二维码（扫了{$qr_match_report['qr_report_name']}二维码），请跟进。", 'bigop', WXUtil::OP_PHONES);
            //     $media_id = WechatMedia::SCAN_QRCODE_TIPS[ENV]['tizhijian'][$qr_match_report['report_type']];
            //     $str = sprintf(WechatMsgTemplate::MSG_COMMON_IMAGE, $toUser, $fromUser, time(), $media_id);
            //     // $str = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, time(), $msg);
            //     return $str;
            // }
        }
        $code = PatientCode::getFreeCodeByOpenid($openid, $prefix, $type, 4);
        if (!$code) {
            $not_push = strpos($eventKey, 'NOTPUSH') ? 1 : 0;
            list($id, $code) = PatientCode::initCode($openid, $prefix, $not_push, $type, 4);
        }

        $check_log_remark = ['data' => ['event' => (string)$this->post->Event, 'event_key' => (string)$this->post->EventKey, 'camera' => $camera ?? '', 'product' => $product]];
        if (strtolower($this->post->Event) == 'subscribe') {
            $check_log_remark['lang'] = "扫码关注了公众号";
        } elseif ($this->post->Event == 'SCAN') {
            $check_log_remark['lang'] = "扫码进入了公众号";
        } elseif ($this->post->Event == 'CLICK') {
            $check_log_remark['lang'] = "点击了自定义菜单";
        }
        CheckLog::addLogInfo(0, 'wechat_third_callback_event', $check_log_remark, 0, '', $code);

        if ($sn) {
            if ($plain_sn) {
                SnPcode::createSnPcode(['pcode' => $code, 'sn' => $plain_sn, 'user_id' => $user_id]);
            }
            PatientCode::updateUserInfo($code, $user['org_id'], $user_id);
            if ($user) {
                $org = $user['org'];
            }
        } else if ($org_id) {
            $organizer = new Organizer();
            $org = $organizer->getOrganizerById($org_id);
        }
        $url = EYE_DOMAIN . 'api/wechat/jump?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code);
        if (strpos($eventKey, 'IVAK_GETCODE_HOSPITAL_ORG')) {
            $url = EYE_DOMAIN . 'api/wechat/jump?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . '&org_id=' . $org_id;
            if ($org['age_type'] != 2) {
                $url .= '&noid=1';
            }
        } elseif ($sn && strpos('pre' . $eventKey, 'THIRD')) {
            // FD16 体知健
            $show_fd16_video_str = intval($user['org']['config']['show_fd16_video']) === 1 ? '&show_fd16_video=1' : '';
            $show_fd16_qrcode_str = intval($user['org']['config']['show_fd16_qrcode']) === 1 || $camera['work_mode'] == 4 ? '&show_fd16_qrcode=1' : '';
            $pay_str = '';
            $work_mode_str = '&work_mode=' . $camera['work_mode'];
            $age_type = $user['org']['age_type'];
            $register_type = ($user['config']['register_type'] == -1 || !isset($user['config']['register_type'])) ? $user['org']['config']['register_type'] : $user['config']['register_type'];
            $camera['work_mode'] == 4 && $register_type = 1;
            $register_type_str = '&register_type=' . $register_type;
            // jira- 1421 如果 机构配置公众号 并且微信有支付功能 跳转支付页面
            if ($this->wechat_config['relation_payment'] && $user['org']['config']['wechat_id']) {
                $url = EYE_DOMAIN . 'api/wechat/payment?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . "&noid=1&is_fd16=1&sn={$sn}&age_type={$age_type}{$pay_str}";
            } else {
                $url = EYE_DOMAIN . 'api/wechat/jump?en_open_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($openid)) . '&pcode=' . urlencode($code) . "&noid=1&is_fd16=1&sn={$sn}&age_type={$age_type}{$pay_str}";
            }
            $url .= $show_fd16_video_str . $show_fd16_qrcode_str . $work_mode_str . $register_type_str;
        }
        if (isset($org['age_type'])) { // AK-1230：(0 || null)填写生日;1填写年龄
            $url .= '&age_type=' . $org['age_type'];
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
        if ($product == 1) {
            $url .= "&is_huixintong=1";
            $product_name = '鹰瞳医疗';
        } elseif ($product == 2) {
            $url .= "&is_yingtong=1";
            $product_name = '鹰瞳健康';
        } elseif ($product == 3) {
            $url .= "&is_zhongyou=1";
            $product_name = '鹰瞳健康';
        } elseif ($product == 4) {
            $url .= "&is_tzj=1";
            $product_name = '鹰瞳健康';
        } elseif ($product == 5) {
            $url .= "&is_yt_health=1";
            $product_name = '鹰瞳健康';
        }

        if ($org && $org['config']['rigister_miniprogram'] || $user['org']['config']['rigister_miniprogram']) {
            // $msg = '<a data-miniprogram-appid=' . REGISTER_WX_APPID . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '" href="' . $url . '">填写信息开始检测>></a>';
            // $msg = '<a data-miniprogram-appid=' . REGISTER_WX_APPID . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '">填写信息开始检测>></a>';
            $wx_util = new WXUtil($this->wechat_config['appid'], $this->wechat_config['secret']);
            $template_id = $this->wechat_config['template'][1]['template_id'];
            if (!$template_id || !$this->wechat_config['applet']['appid']) {
                $msg = '<a href="' . $url . '">👉点击此处填写信息>></a>';
                $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
            } else {
                if (ENV == 'test' && 0) {
                    $msg = '<a data-miniprogram-appid=' . $this->wechat_config['applet']['appid'] . ' data-miniprogram-path="' . WXUtil::h5Url2Miniprogram($url) . '">点击此处填写信息>></a>';
                    Logger::info('test_openid= ' . $this->request->REQUEST['openid'] . ' url=' . $url . ' mini:' . WXUtil::h5Url2Miniprogram($url), 'third_callback');
                    $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
                } else {
                    WechatUserCheck::sendRegisterMiniprogram($wx_util, $template_id, $openid, WXUtil::h5Url2Miniprogram($url), $product_name, $this->wechat_config['applet']['appid']);
                    $info = '';
                }
            }
        } else {
            $msg = '<a href="' . $url . '">👉点击此处填写信息>></a>';
            $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
        }
        Logger::info('openid= ' . $this->request->REQUEST['openid'] . ' url=' . $url . ' mini:' . WXUtil::h5Url2Miniprogram($url), 'third_callback');
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
            // if ($this->isWorkTime() || strpos('pre' . $text, 'wwww')) {
            //     Logger::info("[{$toUser}] [$text] [abb_work]", 'wechat_sevice_qr');
            //     $filename = ROOT_PATH . '/config/assets/service_qywx_qr.png';
            // } else {
            //     Logger::info("[{$toUser}] [$text] [abb_out]", 'wechat_sevice_qr');
            //     $filename = ROOT_PATH . '/config/assets/service_qywx_qr.png';
            // }
            // if (strpos('pre' . $text, 'oooo')) {
            $filename = ROOT_PATH . '/config/assets/service_tzjwx_qr_1.png';
            //}
            $template = WechatMsgTemplate::MSG_COMMON_IMAGE;
            $wx_util = new WXUtil($this->wechat_config['appid'], $this->wechat_config['appid']);
            $media_id = $wx_util->uploadImageMedia($filename);
            $info = sprintf($template, $toUser, $fromUser, $time,  $media_id);
            Logger::info($info, 'wechat_sevice_qr_tizhijian');
        }
        return $info;
    }

    private function handleFD16($eventKey)
    {
        $arr = explode('_', $eventKey);
        $sn = array_pop($arr);
        $prefix = $this->getCodePrefix($eventKey);
        // prefix is 8996
        echo $this->getPatientInfoLink($prefix, $sn);
        return;
    }
    private function handleTextMsg()
    {
        $msg = '您的视网膜体知健筛查报告正在生成中。报告生成后，您将收到一条微信通知。';
        $toUser = $this->post->FromUserName;
        $fromUser = $this->post->ToUserName;
        $time = $this->post->CreateTime;
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
        } else {
            $check_id = (int) $this->post->EventKey;
        }
        $cobj = new CheckInfo();
        $cobj->setCache(0);
        $check_info = $cobj->getCheckDetail($check_id);
        $check_info = $check_info[0];
        //bcd院内套餐（不是体验）
        //院外或者套餐A或者体验，首次评估就推送
        //if ($check_info['review_status'] >= CheckInfo::REVIEW_DONE && ($check_info['patient_info_status'] == 3 || $check_info['package_type'] == 0 || $check_info['type'] == 1)
        //    || in_array($check_info['review_status'], [40, 20]) && $check_info['patient_info_status'] == 2 && $check_info['package_type'] > 0 && $check_info['type'] == 0)
        if ($check_info['review_status'] >= CheckInfo::REVIEW_DONE && $check_info['review_status'] != CheckInfo::REVIEW_STOP) {
            // BAEQ-1331 未本地审核不推送报告
            if (!($check_info['is_retina'] == 2 && $check_info['review_status'] == CheckInfo::REVIEW_DONE)) {
                $ret = WechatUserCheck::sendMsgByOpenId(['open_id' => $this->openid], $check_info, 1);
            }
            $db_status = 1;
            if (!$ret) {
                $db_status = 0;
            }
            $info = '';
        } else {
            $db_status = 0;
            $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
        }
        // BAEQ-1331 本地医生审核后签字，但是还没审核
        if ($check_info['is_retina'] == 2 && $check_info['review_status'] == CheckInfo::REVIEW_DONE) {
            $db_status = 2;
            $info = sprintf(WechatMsgTemplate::MSG_COMMON_TEXT, $toUser, $fromUser, $time, $msg);
        }
        if (!(ENV == 'production' && !in_array($check_info['org_id'], [1, 5129]))) {
            $item = WechatUserCheck::addItem(['open_id' => $this->openid, 'check_id' => $check_id, 'status' => $db_status]);
        }
        //$alarms = HandleAlarm::getAlarmByCheckIds($check_id);
        // 有警示单，新扫码的人
        // if ($alarms && $item['open_id'] && !isset($item['updated'])) {
        //     $alarm = $alarms[$check_id];
        //     $witem = ['open_id' => $this->openid, 'check_id' => $check_id, 'new_wechat' => IS_NEW_WX];
        //     $witem['name'] = $check_info['patient']['name'];
        //     WechatUserCheck::sendWarningMsg($witem, $alarm);
        // }
        return $info;
    }
}
