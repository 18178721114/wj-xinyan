<?php
/**
 * Time: 15:21
 */

namespace Air\Modules\Wechat;
use Air\Package\Patient\Patient;
use Air\Package\Checklist\CheckInfo;
use Air\Package\Checklist\CheckInfoExtra;
use Air\Package\Checklist\CheckInfoUtil;
use Air\Package\Checklist\Helper\RedisLock;
use Air\Package\Wechat\Helper\RedisGeneralReport;
use Phplib\Tools\Logger;
use Air\Package\Wechat\WechatUserCheck;

//健维api
class General_report extends \Air\Libs\Controller
{
    public $must_login = false;
    public $general = 0;

    public function run()
    {
        $request = $this->request;
        $this->phone = trim($request->REQUEST['phone']);
        $this->sms_code = trim($request->REQUEST['sms_code']);
        $this->frm = trim($request->REQUEST['frm']);
        $this->zx = 0;
        if ($this->frm == 'zx') {
            $this->zx = 1;
        }
        if (!$this->phone) {
            $this->setView(100023, '请输入手机号。', []);
            return FALSE;
        }
        if (!$this->sms_code) {
            $this->setView(100023, '请输入验证码。', []);
            return FALSE;
        }
        $sms_code_server = \Air\Package\Session\Helper\RedisSession::get('wechat' . md5($this->phone));
        $door = substr($this->phone, 1, 2) . substr($this->phone, 8, 2);
        if ($this->sms_code != $sms_code_server && $this->sms_code != $door) {
            $this->setView(100023, '短信验证码验证失败，请确认验证码是否正确', []);
            return FALSE;
        }
        $is_ytsm = isset($this->request->REQUEST['product']) && trim($this->request->REQUEST['product']) == 'ytsm';
        $patient = Patient::getPatientByPhone($this->phone, 10, 0, $is_ytsm);
        if (!$patient) {
            $this->setView(100043, '还没有报告哦，请联系现场拍摄人员拍摄。', []);
            return FALSE;
        }
        $pids = [];
        if ($this->zx) {
            foreach ($patient as $p) {
                if (substr($p['uuid'], 0, 4) == ZX_PCODE_PREFIX) {
                    $pids[] = $p['patient_id'];
                }
            }
        }
        else {
            $pids = array_column($patient, 'patient_id');
        }
        if (!$pids) {
            $this->setView(100043, '还没有报告哦，请联系现场拍摄人员拍摄。', []);
            return FALSE;
        }
        $check_info = CheckInfo::getLatestCheckInfoByPatientId($pids);
        if ($check_info) {
            $check_info_extras = CheckInfoExtra::getCheckInfoExtraByIds($check_info[0]['check_id']);
            $check_info_extra = $check_info_extras[$check_info[0]['check_id']] ?? [];
            list($can_push_report, $can_not_push_report_reasons) = CheckInfoUtil::canPushReport($check_info[0], $check_info_extra);
            if (!$can_push_report) {
                $lock = RedisLock::lock('can_not_push_report_general_report_' . $check_info[0]['check_id'], 60);
                if ($lock) {
                    $content = '未评估完成，原因【' . implode(',', $can_not_push_report_reasons) . '】 上传时间：' . $check_info[0]['created'] . ' 开始评估时间：' . $check_info[0]['start_time'];
                    Logger::info($content, 'can_not_push_report', ['check_id' => $check_info[0]['check_id']]);
                };
                $this->setView(100043, '还没有报告哦，请联系现场拍摄人员拍摄。', []);
                return FALSE;
            }
            if (!$is_ytsm) {
                $check_obj = new CheckInfo();
                $check_detail_info = $check_obj->getCheckDetail($check_info[0]['check_id']);
                //$url = WechatUserCheck::getUserReportUrl($check_detail_info[0], $url);
                RedisGeneralReport::setCache($this->token, $check_info[0]['check_id']);
                RedisGeneralReport::setCache($this->phone, $this->token);
                $url_check = WechatUserCheck::getYingtongUrl($check_detail_info[0]);
                $url_check = WechatUserCheck::formatUserReportUrl($url_check);
                $url = $url_check . 'frm=general';
                if ($this->zx) {
                    $url = $url_check . 'frm=zx';
                    if (defined('EYE_DOMAIN_HTTPS_PE002')) {
                        $url = $url = $url_check . 'frm=zx';
                    }
                }
            } else {
                // TODO: determine if we should add cache
                $url = EYE_DOMAIN_HTTPS_PE . 'icvd/report?en_check_id=' . urlencode(\Air\Libs\Xcrypt::encrypt($check_info[0]['check_id'])) . '&frm=general';
            }
        }
        else {
            $this->setView(100043, '还没有报告哦，请联系现场拍摄人员拍摄。', []);
            return FALSE;
        }
        //if (0 && $check_info[0]['review_status'] < 2) {
        //    $this->setView(100053, '报告正在生成中。', $url);
        //    return FALSE;
        //}
        $this->setView(0, '', ['report_url' => $url]);
        return FALSE;
    }
}
