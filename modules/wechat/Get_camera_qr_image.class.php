<?php

namespace Air\Modules\Wechat;

use Air\Libs\Base\Utilities;
use Air\Package\Fd16\Camera;
use Air\Package\Fd16\CameraHandler;
use Air\Package\User\User;
use Air\Package\Wechat\WechatThird;
use Air\Package\Wechat\WXUtil;

/**
 * 供鹰瞳健康获取二维码，放到pdf报告中
 */
class Get_camera_qr_image extends \Air\Libs\Controller
{
    const ALLOW_IPS = [
        '101.200.85.230',
        '59.110.49.59',
        '36.112.64.2',
        '39.107.84.77',
        '116.247.81.186',
        '123.57.216.175', // 小瞳助手测试环境
    ];
    const REPORT_TYPE = [
        //'annuity_report',
        'woman_report',
        // 'child_report',
    ];

    public function run()
    {
        $ip = Utilities::getClientIP('string');
        if (!in_array($ip, self::ALLOW_IPS)) {
            $this->setView(0, 'ip不在白名单中', '');
            //return false;
        }
        if (!$this->_init()) {
            return false;
        }
        $request = $this->request->REQUEST;
        $obj = new WXUtil(WX_APPID, WX_SECRET);
        if ($request['ikang']) {
            // $obj = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);
            $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
        } elseif ($request['icvd']) {
            $obj = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
        } elseif ($request['zhongyou']) {
            $obj = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
        } elseif ($request['tizhijian']) {
            $obj = new WXUtil(TZJ_WX_APPID, TZJ_WX_SECRET);
        } elseif ($request['yt_health']) {
            // $obj = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);
            $obj = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
        } elseif ($request['third_qr'] && $request['sn']) {
            if (strlen($request['sn']) == 32) {
                $camera = CameraHandler::getCameraBySN(trim($request['sn']));
            } else {
                $camera = CameraHandler::getCameraOriginSN(trim($request['sn']));
                $request['sn'] = $camera[0]['md5'];
            }
            if (!$camera) {
                $this->setView(999, 'sn 不存在，请确认sn参数是否正确', '');
                return FALSE;
            }
            if ($camera && !$camera[0]['user_id']) {
                $this->setView(9991, '请绑定账号', '');
                return FALSE;
            }
            $user = new User();
            $user_info = $user->getUserById($camera[0]['user_id']);
            $wechat_id = $user_info['org']['config']['wechat_id'] ?  $user_info['org']['config']['wechat_id'] : 0;
            if (!$wechat_id) {
                $this->setView(9992, '请绑定微信', '');
                return FALSE;
            }
            $wechat_config_data['id'] =  $wechat_id;
            $wechat_config = WechatThird::getWechatConfig($wechat_config_data);
            $obj = new WXUtil($wechat_config['appid'], $wechat_config['secret']);
        }
        if ($request['sn']) {
            $request['sn'] = trim($request['sn']);
            if (strlen($request['sn']) == 32) {
                $camera = CameraHandler::getCameraBySN(trim($request['sn']));
            } else {
                $camera = CameraHandler::getCameraOriginSN(trim($request['sn']));
                $request['sn'] = $camera[0]['md5'];
            }
            if (!$camera) {
                $this->setView(999, 'sn 不存在，请确认sn参数是否正确', '');
                return FALSE;
            }
            // if ($request['yt_health'] && ENV == 'test') {
            //     $url = $obj->createQRLimitStrScene('TZJ_', $request['sn']);
            // } elseif ($request['yt_health']) {
            //     $url = $obj->createQRLimitStrScene('YTHEALTH_', $request['sn']);
            // } else
            if ($request['tizhijian']) {
                $url = $obj->createQRLimitStrScene('TZJ_', $request['sn']);
            } elseif ($request['third_qr']) {
                $url = $obj->createQRLimitStrScene('THIRD_', $request['sn']);
            } else if ($request['fd16']) {
                $url = $obj->createQRLimitStrScene('IVAK_GETCODE_NEWB_NOID_FD16_', $request['sn']);
            } else if ($request['icvd'] || $request['yt_health']) {
                if (isset($request['report_type']) && in_array($request['report_type'], self::REPORT_TYPE)) {
                    $url = $obj->createQRLimitStrSceneCopy($request['report_type'], $request['sn']);
                } elseif ($request['zhongyi']) {
                    $url = $obj->createQRLimitStrSceneCopy('zhongyi', $request['sn']);
                } else {
                    $url = $obj->createQRLimitStrScene('', $request['sn']);
                }
            } else if ($request['zhongyou']) {
                $url = $obj->createQRLimitStrScene('SMB_', $request['sn']);
            } else {
                $url = $obj->createQRLimitStrScene('SMB_', $request['sn']);
            }
        } else if ($request['huantong']) {
            if ($request['str']) {
                $url = $obj->createQRLimitStrScene('icvdchannel_', 'huantong_' . $request['str']);
            }
        } else if ($request['big_camera']) {
            if ($request['before'] && $request['eye'] && $request['payment']) {
                $url = $obj->createQRLimitStrScene('ICVD_BIG_CAMERA_REG_BEFORE_EYE_PAY');
            } elseif ($request['before'] && $request['eye']) {
                $url = $obj->createQRLimitStrScene('ICVD_BIG_CAMERA_REG_BEFORE_EYE');
            } elseif ($request['before']) {
                $url = $obj->createQRLimitStrScene('ICVD_BIG_CAMERA_REG_BEFORE');
            } elseif ($request['eye']) {
                $url = $obj->createQRLimitStrScene('ICVD_BIG_CAMERA_EYE');
            } else {
                $url = $obj->createQRLimitStrScene('ICVD_BIG_CAMERA');
            }
        } elseif ($request['before']) {
            $url = $obj->createQRLimitStrScene('SMB_BEFORE');
        } elseif ($request['big']) {
            $url = $obj->createQRLimitStrScene('SMB_BIG');
        } elseif ($request['intelligent_voice']) {
            //生产智能语音二维码
            $url = $obj->createQRLimitStrScene('INTELLIGENT_VOICE');
        } else {
            $url = $obj->createQRLimitStrScene('SMB_BASIC');
        }

        $this->setView(0, '', $url);
    }

    private function _init()
    {
        $request = $this->request->REQUEST;
        \Phplib\Tools\Logger::info("lock_failed_by_status:", 'Get_camera_qr_image', $request);
        if (
            empty($request['huantong']) &&
            empty(trim($request['big_camera'])) && empty(trim($request['sn'])) && empty($request['basic']) &&
            empty($request['big']) && empty($request['before']) && empty($request['intelligent_voice'])
        ) {
            $this->setView(10003, '缺少参数', []);
            return false;
        }

        return true;
    }
}
