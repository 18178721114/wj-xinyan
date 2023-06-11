<?php

namespace Air\Modules\Wechat;

use \Air\Libs\Xcrypt;
use Air\Package\Checklist\CheckLog;
use \Air\Package\Fd16\CameraHandler;
use \Air\Package\Wechat\WXUtil;
use \Air\Package\Wechat\helper\RedisPcodeImgUrl;
use \Air\Package\Patient\Patient;
use \Air\Package\User\PatientCode;
use \Air\Package\User\User;
use \Air\Package\Wechat\WechatUserCheck;
use \Air\Package\Smb\SnPcode;
use Air\Package\User\VerificationCode;

class Patient_info_add_vcode extends \Air\Libs\Controller
{
    public $must_login = false;
    public $general = 0;
    public $runtime = 1;
    private $fd16_user = [];
    private $fd16_start_url = '';

    public function run()
    {
        if (!$this->_init()) {
            return false;
        }
        $pcode_item = PatientCode::getItemByPcode($this->pcode);

        if (empty($pcode_item)) {
            $this->setView(10001, '筛查码无效，请重新扫码获取。', []);
            return false;
        }
        if ($this->is_fd16) {
            $camera = CameraHandler::getCameraBySN($this->sn);
            $plain_sn = $camera['sn'];
            $user_id = $camera['user_id'];
            $user_obj = new User();
            $this->fd16_user = $user_obj->getUserById($user_id);
        }
        $register_type = ($this->fd16_user['config']['register_type'] == -1 || !isset($user['config']['register_type'])) ? $this->fd16_user['org']['config']['register_type'] : $this->fd16_user['config']['register_type'];
        $this->openid = $pcode_item['openid'];
        $vcode_org_id = in_array($this->fd16_user['org_id'], TAIKANG_ORG_ID) ? TAIKANG_ORG_ID[0] : $this->fd16_user['org_id'];
        $vcode = VerificationCode::checkExist($this->vcode, $vcode_org_id);
        if (!$vcode || $vcode['expire_in'] < date('Y-m-d H:i:s')) {
            $this->setView(10001, $this->code_name . '无效，请联系工作人员！', []);
            return false;
        }
        if ($vcode['check_id']) {
            $this->setView(10012, $this->code_name . '无效，请联系工作人员！', []);
            return false;
        }
        $ret = 0;
        $binded_patient = Patient::getPatientsByUuids($this->pcode);
        if ($binded_patient) {
            if ($binded_patient[$this->pcode]['patient_id'] == $vcode['patient_id']) {
                $ret = 1;
            }
            else {
                $this->setView(10014, '您当前的链接已被使用，请重新扫描设备二维码获取新的链接！', []);
                return false;
            }
        }
        if (!$ret) {
            $ret = Patient::updateUuidByPatientId($this->pcode, $vcode['patient_id']);
        }
        if (!$ret) {
            $this->setView(10013, $this->code_name . '已使用，请联系工作人员！', []);
            return false;
        }
        $ret_pc = PatientCode::updatePatientIdInfo($this->pcode, $vcode['patient_id']);
        // PatientCode::updateInfo($this->pcode, 0, $this->fd16_user['org_id'], $vcode['patient_id'], 0);
        
        $this->patient_id = $vcode['patient_id'];
        $patient = Patient::getPatientsByIds($vcode['patient_id'])[$vcode['patient_id']];
        $this->od_diopter = isset($patient['extra_json']['od_diopter']) ? $patient['extra_json']['od_diopter'] : '-1';
        $this->os_diopter = isset($patient['extra_json']['os_diopter']) ? $patient['extra_json']['os_diopter'] : '-1';
        $this->name = $patient['name'];
        if ($register_type == 0 || $patient['status'] == 0) {
          $this->setView(1, 'fd16', '');
          return true;
        }
        if ($this->is_fd16) {
            $show_fd16_video_str = intval($this->fd16_user['org']['config']['show_fd16_video']) === 1 ? '&show_fd16_video=1' : '';
            $show_fd16_qrcode_str = intval($this->fd16_user['org']['config']['show_fd16_qrcode']) === 1 || $camera['work_mode'] == 4 ? '&show_fd16_qrcode=1' : '';
            // $hxt_plus_agent_str = intval($this->fd16_user['org']['config']['hxt_plus_agent']) === 1 ? '&hxt_plus_agent=1' : '';
            $work_mode_str = '&work_mode=' . $camera['work_mode'];
            $substr6Sn = '&substr6Sn=' . substr($plain_sn, -6);
            // 小相机跳转到相机启动页
            $this->fd16_start_url = EYE_DOMAIN_HTTPS_PE . 'fd16/start?vcode=1&verification_code=' . $this->vcode . '&en_openid=' . urlencode(Xcrypt::encrypt($this->openid))
                . '&pcode=' . urlencode($this->pcode) . '&sn=' . $this->sn . '&ts=' . time() . "&is_yingtong=" . $this->is_yingtong . "&name=" . urlencode($this->name)
                . '&od_diopter=' . $this->od_diopter . '&os_diopter=' . $this->os_diopter . $show_fd16_video_str . $show_fd16_qrcode_str . $work_mode_str . $substr6Sn;

            RedisPcodeImgUrl::setCache($this->pcode . '_fd16_url', $this->fd16_start_url);
            CheckLog::addLogInfo(0, 'patient_info_add_vcode', ['data' => ['vcode' => $this->vcode, 'sn' => $camera['sn'], 'pcode' => $this->pcode]], ($camera['user_id'] ?? 0), '', $this->pcode);
            $this->setView(0, 'fd16', ['h5' => $this->fd16_start_url, 'miniprogram' => WXUtil::h5Url2Miniprogram($this->fd16_start_url)]);
            return true;
        }
        $this->setView(10009, '登记信息失败', []);
        return false;
    }

    private function _init()
    {
        $request = $this->request->REQUEST;
        // if (empty(trim($request['name']))) {
        //     $this->setView(10003, '请输入姓名', []);
        //     return FALSE;
        // }
        $this->get_customers = trim($request['get_customers']);
        $this->code_name = '福利码';
        if ($this->get_customers) {
            $this->code_name = '兑换码';
        }
        $this->vcode = strtoupper(trim($request['verification_code']));
        if (trim($request['pcode'])) {
            $this->pcode = trim($request['pcode']);
            if (strlen(trim($request['pcode'])) >= 32) {
                $this->pcode = Xcrypt::decrypt(trim($request['pcode']));
            }
        }
        if (!$this->vcode) {
            $this->setView(10003, '请提供' . $this->code_name, []);
            return false;
        }
        if (!$this->pcode) {
            $this->setView(10004, '请提供pcode', []);
            return false;
        }
       
        $this->client = trim($request['client']);
        if (!empty(trim($request['en_openid']))) {
            $this->openid = Xcrypt::decrypt(trim($request['en_openid']));
        }
        $this->is_fd16 = 1;
        if (empty(trim($request['is_fd16']))) {
            $this->setView(10005, '仅支持健康扫描仪', []);
            return false;
        }
        $this->sn = trim($request['sn']);
        if (empty(trim($request['sn']))) {
            $this->setView(10005, '健康扫描仪的序列号不能为空', []);
            return false;
        }
        $this->is_yingtong = trim($request['is_yingtong']);
        return true;
    }

    public function asyncJob()
    {
        if (
            $this->view['error_code'] == 0 &&
            substr($this->pcode, 0, 4) === ICVD_PCODE_PREFIX
            && substr($this->openid, 0, 5) === ICVD_WX_OPENID_PREFIX
            && $this->img_url
        ) {
            WechatUserCheck::sendImageByOpenId($this->name, $this->openid, $this->img_url, $this->pcode, 2, 1);
        } elseif (in_array($this->view['error_code'], [0, 1])) {
            if (!empty($this->sn) && $this->is_fd16) {
                $camera = CameraHandler::getCameraBySN($this->sn);
                $plain_sn = $camera['sn'];
                $user_id = $camera['user_id'];
                SnPcode::createSnPcode(['pcode' => $this->pcode, 'sn' => $plain_sn, 'user_id' => $user_id]);
                if ($this->fd16_start_url) {
                    if (defined('SWITCH_REGISTER_MINIPROGRAM') && ($this->fd16_user['org']['config']['rigister_miniprogram'] || SWITCH_REGISTER_MINIPROGRAM)) {
                        $url = WXUtil::h5Url2Miniprogram($this->fd16_start_url);
                        $miniprogram = 1;
                    } else {
                        $url = $this->fd16_start_url;
                        $miniprogram = 0;
                    }
                    !$miniprogram && WechatUserCheck::sendImageByOpenId($this->name, $this->openid, $url, $this->vcode, $this->is_new_wechat, 0, 1, $miniprogram);
                    RedisPcodeImgUrl::setCache($this->pcode . '_fd16_url', $this->fd16_start_url);
                }
            }
        }
    }
}
