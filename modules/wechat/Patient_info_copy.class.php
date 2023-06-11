<?php

namespace Air\Modules\Wechat;

use Air\Libs\Xcrypt;
use Air\Package\Cache\RedisCache;
use Air\Package\Checklist\CheckLog;
use Air\Package\Fd16\CameraHandler;
use Air\Package\Patient\Patient;
use Air\Package\Pay\CheckOrder;
use Air\Package\Smb\SnPcode;
use Air\Package\User\Helper\DBOrganizerHelper;
use Air\Package\User\Organizer;
use Air\Package\User\PatientCode;
use Air\Package\User\User;
use Air\Package\User\VerificationCode;
use Air\Package\Wechat\helper\RedisPcodeImgUrl;
use Air\Package\Wechat\WechatUserCheck;
use Air\Package\Wechat\WXUtil;
use Phplib\Tools\Logger;

class Patient_info_copy extends \Air\Libs\Controller
{
    public $must_login = false;
    public $general = 0;
    private $fd16_user = [];
    private $fd16_start_url = '';

    /**
     * 是否是从小程序ULR/Scheme跳转过来
     * @param $patient
     * @return bool
     */
    static public function isFromWxaPath($patient)
    {
        return array_key_exists('wxa', $patient['extra_json']) && $patient['medical_record_no'];
    }

    public function run()
    {
        if (!$this->_init()) {
            return false;
        }
        if ($this->org_id) {
            $params['org_id'] = $this->org_id;
        }

        $pcode_item = PatientCode::getItemByPcode($this->pcode);
        if (empty($pcode_item) && !$this->has_vcode) {
            $this->setView(10001, '筛查码无效，请重新扫码获取。', []);
            return false;
        }
        $this->is_new_wechat = $pcode_item['new_wechat'];
        $this->openid = $pcode_item['openid'];
        $old_patient = Patient::getPatientsByIds($this->pid)[$this->pid];
        $this->uuid = $old_patient['uuid'];
        $old_pcode_item = PatientCode::getItemByPcode($this->uuid);
        if (empty($old_pcode_item)) {
            $this->setView(10002, '筛查码无效，请重新扫码获取。', []);
            return false;
        }
        if ($pcode_item['openid'] != $old_pcode_item['openid'] && !$this->pa_model) {
            $this->setView(10003, '权限不够，请重新扫码获取。', []);
            return false;
        }

        $org_id = $pcode_item['org_id'];
        if ($this->is_fd16) {
            $camera = CameraHandler::getCameraBySN($this->sn);
            $user_id = $camera['user_id'];
            $user_obj = new User();
            $this->fd16_user = $user_obj->getUserById($user_id);
            $params['org_id'] = $org_id = $this->fd16_user['org_id'] ? $this->fd16_user['org_id'] : $this->org_id;
            if (
                strpos($this->openid, WX_OPENID_PREFIX) === 0
                && $this->fd16_user['config']['pay_config']
                && is_numeric($this->fd16_user['config']['pay_config'])
                && bccomp($this->fd16_user['config']['pay_config'], 0.01, 2) >= 0
                && !in_array($this->fd16_user['org']['config']['business_line'], [3, 4]) // ICVD和MV不走以下流程
            ) {
                if (!$pcode_item['user_id']) {
                    $this->setView(10011, '筛查码无效，请重新扫码获取。', []);
                    return false;
                }
                // $check_order = CheckOrder::checkExistByPcode($pcode_item['pcode']);
                // if (!$check_order || $check_order[0]['status'] == 0) {
                //     $this->setView(10012, '未支付成功，请重新扫码支付。', []);
                //     return false;
                // }
            }
        }
        $org_obj = new Organizer();
        $org = $org_obj->getOrganizerById($org_id);
        //必填项配置
        $register_required_options = json_decode($org['config']['register_required_options'], true);

        // $new_patient = Patient::getPatientByUuid($this->uuid);
        if ($this->has_vcode) {
            $this->pcode = $this->has_vcode;
        }
        $patient = Patient::getPatientByUuid($this->pcode);
        Logger::info(['patient' => $patient, 'old_patient' => $old_patient, 'isFromWxaPath' => $this::isFromWxaPath($patient)], 'patient_info_copy');
        if ($old_patient && $this::isFromWxaPath($patient)) {
            $_patient = new Patient();
            $_patient->updatePatient([
                'patient_id' => $patient['patient_id'],
                'birthday' => $old_patient['birthday'],
                'gender' => $old_patient['gender'],
                'name' => $old_patient['name'],
                'status' => 1,
                'extra' => $old_patient['extra'],
                'phone' => $old_patient['phone'],
                'insurance_text' => $old_patient['insurance_text'],
            ]);
        }
        if (!$patient || $patient['status'] == 0) {
            if ($patient['patient_id']) {
                $this->patient_id = $patient['patient_id'];
            }
            $patient_obj = new Patient();
            if ($old_patient) {
                if ($this->patient_id) {
                    if ($this->patient_id != $pcode_item['patient_id']) {
                        $this->setView(10001, '筛查码无效，请重新扫码获取。', []);
                        return false;
                    }
                }
                $update_data = [
                    'insurance_text' => $this->insurance_text ? json_encode($this->insurance_text, JSON_UNESCAPED_UNICODE) : '',
                ];
                if ($this->phone) {
                    $update_data['phone'] = Xcrypt::aes_encrypt($this->phone);
                }
                // BAEQ-3210 蓝颐
                $this->medical_record_no = RedisCache::getCache('pcode_2_medical_record_no_' . $this->pcode);
                if ($this->medical_record_no) {
                    $update_data['medical_record_no'] = $this->medical_record_no;
                }
                $this->patient_id = $patient_obj->copyAddPatient($this->pid, $this->pcode, $org_id, $update_data);
            } else {
                $this->setView(10010, '登记信息失败', []);
                return false;
            }
            $patient = Patient::getPatientByUuid($this->pcode);
        }

        // 健康管理账号创建
        // if ($params['org_id'] && $pcode_item['openid']) {
        //     $prefix = substr($pcode_item['openid'], 0, 5);
        //     if (in_array($prefix, [YTHEALTH_WX_OPENID_PREFIX])) {
        //         $org = Organizer::getOrganizerSelfById($params['org_id'])[0];
        //         if ($org && $org['customer_id'] == 27) {
        //             $c_end_user = CEndUser::onCreatePatient($patient['patient_id'], ['openid' => $pcode_item['openid']]);
        //             if ($c_end_user) {
        //                 $c_end_user_params['patient_id'] = $patient['patient_id'];
        //                 $c_end_user_params['extra_json'] = $c_end_user;
        //                 $p_obj = new Patient();
        //                 $p_obj->updatePatient($c_end_user_params);
        //             }
        //         }
        //     }
        // }

        if ($this->has_vcode) {
            if ($this->patient_id) {
                $this->expire_in = date('Y-m-d H:i:s', strtotime('+1days'));
                $this->vcode = VerificationCode::allocateCode($params['org_id'], $this->patient_id, $this->expire_in);
                CheckLog::addLogInfo(0, 'patient_info_copy_vcode', ['data' => ['patient_id' => $this->patient_id, 'vcode' => $this->vcode]], 0, '', $this->pcode);
                $this->setView(0, '获取福利码成功', ['h5' => $this->vcode, 'miniprogram' => $this->vcode]);
                return true;
            } else {
                $this->setView(10010, '登记信息失败', []);
                return false;
            }
        }
        $this->name = $patient['name'];
        $this->od_diopter = isset($patient['extra_json']['od_diopter']) ? $patient['extra_json']['od_diopter'] : '-1';
        $this->os_diopter = isset($patient['extra_json']['os_diopter']) ? $patient['extra_json']['os_diopter'] : '-1';
        //判断历史数据中的必填字段是否有值
        foreach ($patient as $key => $val) {
            if (Organizer::isRequired($key, $register_required_options) && (empty($val) || !$val)) {
                $this->setView(10013, '既往信息不完整，缺少必填项，请补充完整后再次进行提交。', []);
                return false;
            }
        }
        if ($this->is_fd16) {
            # 启动相机前是否需要支付
            $show_fd16_video_str = intval($this->fd16_user['org']['config']['show_fd16_video']) === 1 ? '&show_fd16_video=1' : '';
            $show_fd16_qrcode_str = intval($this->fd16_user['org']['config']['show_fd16_qrcode']) === 1 || $camera['work_mode'] == 4 ? '&show_fd16_qrcode=1' : '';
            $hxt_plus_agent_str = intval($this->fd16_user['org']['config']['hxt_plus_agent']) === 1 ? '&hxt_plus_agent=1' : '';
            $work_mode_str = '&work_mode=' . $camera['work_mode'];
            if ($this->pa_model == 3) {
                $work_mode_str = '&work_mode=4&pa_model=3';
            }
            // 小相机跳转到相机启动页
            $params = '?en_openid=' . urlencode(Xcrypt::encrypt($this->openid))
                . '&pcode=' . urlencode($this->pcode)
                . '&sn=' . $this->sn
                . '&ts=' . time()
                . "&is_yingtong=" . $this->is_yingtong
                . "&name=" . urlencode($this->name)
                . '&od_diopter=' . $this->od_diopter
                . '&os_diopter=' . $this->os_diopter
                . $show_fd16_video_str
                . $show_fd16_qrcode_str
                . $hxt_plus_agent_str
                . $work_mode_str;
            $open_payment = DBOrganizerHelper::is_third_party_pay($org);
            $check_order = CheckOrder::checkExistByPcode($this->pcode);
            if (count($check_order) > 1) {
                $this->setView(10011, gettext('筛查码无效，请重新扫码获取。'), []);
                return false;
            }
            $check_order = $check_order[0];
            # 需要支付且未支付
            $need_pay = ($open_payment and !CheckOrder::isPay($check_order));
            Logger::info(['need_pay' => $need_pay, 'check_order' => $check_order, $this->pcode, $this->openid], 'patient_info_copy');
            if ($need_pay) {
                $this->fd16_start_url = 'pages/payAndCheck/fd16PayAndCheck' . $params;
            } else {
                $this->fd16_start_url = EYE_DOMAIN_HTTPS_PE . 'fd16/start' . $params;
            }

            if (empty($pcode_item['patient_id'])) {
                PatientCode::updatePatientIdInfo($this->pcode, $this->patient_id);
            }
            RedisPcodeImgUrl::setCache($this->pcode . '_fd16_url', $this->fd16_start_url);
            CheckLog::addLogInfo(0, 'patient_info_copy_fd16', ['data' => ['patient_id' => $this->patient_id, 'pcode' => $this->pcode]], 0, '', $this->pcode);
            $this->setView(0, 'fd16', [
                'h5' => $this->fd16_start_url,
                'miniprogram' => WXUtil::h5Url2Miniprogram($this->fd16_start_url),
                'need_pay' => $need_pay
            ]);
            return true;
        } else {

            // 大相机，跳转到筛查码
            $this->img_url = PatientCode::generateScreenImage($this->pcode, $this->name, $this->org_id, $this->request->REQUEST['hxt_plus_agent'], $this->openid);
            if ($this->img_url) {
                PatientCode::updatePatientIdInfo($this->pcode, $this->patient_id);
                $this->setView(0, '登记信息成功1', ['h5' => $this->img_url, 'miniprogram' => $this->img_url]);
                CheckLog::addLogInfo(0, 'patient_info_copy_big', ['data' => ['patient_id' => $this->patient_id, 'pcode' => $this->pcode]], 0, '', $this->pcode);
                return true;
            }
        }

        $this->setView(10009, '登记信息失败', []);
        return false;
    }

    private function _init()
    {
        $request = $this->request->REQUEST;
        if (trim($request['pcode'])) {
            $this->pcode = trim($request['pcode']);
            if (strlen(trim($request['pcode'])) >= 32) {
                $this->pcode = Xcrypt::decrypt(trim($request['pcode']));
            }
        }
        $this->client = trim($request['client']);
        $this->pa_model = trim($request['pa_model']);
        $this->org_id = trim($request['org_id']);
        $this->en_openid = trim($request['en_openid']);
        $this->pid = trim($request['pid']);
        if (!$this->pid) {
            $this->setView(10001, '缺少参数pid', []);
            return false;
        }
        if (!empty(trim($request['en_openid']))) {
            $en_openid = rawurldecode(str_replace(' ', '+', trim($request['en_openid'])));
            $this->openid = Xcrypt::decrypt($en_openid);
        }
        $this->is_fd16 = 0;
        if (!empty(trim($request['is_fd16']))) {
            $this->is_fd16 = trim($request['is_fd16']);
        }
        $this->sn = trim($request['sn']);
        $this->phone = trim($request['phone']);
        if ($this->pa_model) {
            $this->phone = Xcrypt::aes_decrypt(Xcrypt::decrypt($this->en_openid));
        }
        if ($this->pa_model == 2) {
            $this->has_vcode = 'vcode_' . $this->phone . "_" . date('YmdHis');
        }
        if (!$this->pcode && !$this->has_vcode) {
            $this->setView(10001, '缺少参数pcode', []);
            return false;
        }
        if (trim($request['agent_num']) && $request['agent_num'] != 'undefined') {
            $this->insurance_text['customer_manager_id'] = trim($request['agent_num']);
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
        } elseif ($this->view['error_code'] == 0) {
            // 慧心瞳小相机
            if (!empty($this->sn) && $this->is_fd16) {
                $camera = CameraHandler::getCameraBySN($this->sn);
                $plain_sn = $camera['sn'];
                $user_id = $camera['user_id'];
                SnPcode::createSnPcode(['pcode' => $this->pcode, 'sn' => $plain_sn, 'user_id' => $user_id]);
                if ($this->fd16_start_url && $this->client !== 'miniprogram') {
                    WechatUserCheck::sendImageByOpenId($this->name, $this->openid, $this->fd16_start_url, $this->pcode, $this->is_new_wechat, 0, 1);
                    RedisPcodeImgUrl::setCache($this->pcode . '_fd16_url', $this->fd16_start_url);
                }
            } else {
                $url = EYE_DOMAIN_HTTPS_PE . 'user/showImg/' . urlencode($this->img_url);
                WechatUserCheck::sendImageByOpenId($this->name, $this->openid, $url, $this->pcode, $this->is_new_wechat, 0, 1);
            }
        }
    }
}
