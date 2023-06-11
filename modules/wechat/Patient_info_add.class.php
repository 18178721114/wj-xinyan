<?php

namespace Air\Modules\Wechat;

use \Air\Libs\Base\Utilities;
use \Air\Libs\Xcrypt;
use Air\Package\Bisheng\BishengUtil;
use Air\Package\C_end_user\CEndUser;
use Air\Package\Cache\RedisCache;
use Air\Package\Checklist\CheckInfo;
use Air\Package\Checklist\CheckInfoExtra;
use Air\Package\Checklist\CheckLog;
use Air\Package\Cps_mv\MacularVisualImpairment;
use Air\Package\Customfield\CustomFields;
use \Air\Package\Fd16\CameraHandler;
use \Air\Package\Session\Helper\RedisSession;
use Air\Package\User\Helper\DBOrganizerHelper;
use \Air\Package\Wechat\WXUtil;
use \Air\Package\Wechat\helper\RedisPcodeImgUrl;
use \Air\Package\Patient\Patient;
use Air\Package\Pay\CheckOrder;
use Air\Package\Pingan\PAUtil;
use Air\Package\Sat\SatUser;
use \Air\Package\User\PatientCode;
use \Air\Package\User\User;
use \Air\Package\Wechat\WechatUserCheck;
use \Air\Package\Smb\SnPcode;
use Air\Package\Thirdparty\ManniuHandler;
use Air\Package\Thirdparty\ZhongyingHandler;
use Air\Package\User\Organizer;
use Air\Package\User\VerificationCode;
// use Air\Package\Young\Helper\DBYoungCheckVisionInfoHelper;
use \Air\Package\Sme\SMEConfig;
use Phplib\Tools\Logger;

class Patient_info_add extends \Air\Libs\Controller
{
    public $must_login = false;
    public $general = 0;
    private $fd16_user = [];
    public $runtime = 1;
    private $fd16_start_url = '';

    public function run()
    {
        $request = $this->request->REQUEST;
        if (!$this->_init()) {
            return false;
        }
        $name = $this->name;
        $gender = $this->gender;
        $birthday = $this->birthday;
        $phone = $this->phone ? $this->phone : '';
        $pcode = $this->pcode ? $this->pcode : '';
        $height = $this->height ? $this->height : 0;
        $weight = $this->weight ? $this->weight : 0;
        $insurance_text = $this->insurance_text;
        $data = [];
        $data['medical_history'] = $this->medical_history;
        if ($this->complained) {
            $data['complained'] = $this->complained;
        }

        $params = [
            'uuid' => $this->pcode,
            'birthday' => $birthday,
            'gender' => $gender,
            'name' => $name,
            'status' => 1,
            'extra' => $data ? serialize($data) : '',
            'phone' => $phone,
            'insurance_text' => $insurance_text ? $insurance_text : '',
            'medical_record_no' => $this->medical_record_no
        ];
        if ($this->has_vcode) {
            $params['uuid'] = $this->has_vcode;
        }
        if ($height) {
            $params['height'] = $height;
        }
        if ($weight) {
            $params['weight'] = $weight;
        }

        if ($this->id_number) {
            $params['id_number_crypt'] = $this->id_number;
        }

        if ($this->org_id) {
            $params['org_id'] = $this->org_id;
        }

        $extra_json = [];
        if ($this->is_fd16 && $request['salesman_user_id'] > 0) {
            if (isset($request['salesman_user_id']) && !empty($request['salesman_user_id'])) {
                $extra_json['salesman_user_id'] = $request['salesman_user_id'];
            }
            if (isset($request['salesman_auth']) && !empty($request['salesman_auth'])) {
                $extra_json['salesman_auth'] = $request['salesman_auth'];
            }
            //获取sat user
            $sat_user = SatUser::getLinesByRole(['sn' => $this->plain_sn, 'status' => 0, 'role' => [1, 2]]);
            if (empty($sat_user) || !in_array($request['salesman_user_id'], array_column($sat_user, 'user_id'))) {
                $this->setView(10001, gettext('业务员无效！'), '');
                return FALSE;
            }
        }
        if ($this->third_id_name) {
            $extra_json['third_id_name'] = $this->third_id_name;
        }
        if ($this->manniu_id) {
            $extra_json['manniu_id'] = $this->manniu_id;
        }
        if ($this->ssy) {
            $extra_json['ssy'] = $this->ssy;
        }
        if ($this->szy) {
            $extra_json['szy'] = $this->szy;
        }

        $extra_json['od_diopter'] = $this->od_diopter;
        $extra_json['os_diopter'] = $this->os_diopter;

        $extra_json['right_sphere'] = $this->right_sphere;
        $extra_json['left_sphere'] = $this->left_sphere;
        $extra_json['right_column_mirror'] = $this->right_column_mirror;
        $extra_json['left_column_mirror'] = $this->left_column_mirror;

        if (isset($request['hongmei_questionnaire']) && $request['hongmei_questionnaire']) {
            //'上海虹梅机构调查问卷，问卷是hardcode，地址api/checklist/hongmei_questionnaire';
            $extra_json['hongmei_questionnaire'] = htmlspecialchars_decode($request['hongmei_questionnaire']);
        }
        if (isset($request['register_questionnaire']) && $request['register_questionnaire']) {
            //'注册组收集数据'; jira-1260
            $extra_json['register_questionnaire'] = htmlspecialchars_decode($request['register_questionnaire']);
        }
        if (isset($request['occupation']) && $request['occupation'] && Organizer::isRequired('occupation', $this->register_required_options)) {
            $extra_json['occupation'] = $request['occupation'];
        }
        if ($this->gestational) {
            $extra_json['gestational'] = $this->gestational;
        }
        if ($this->company) {
            $extra_json['company'] = $this->company;
        }
        if ($this->industry) {
            $params['industry'] = $this->industry;
        }
        if ($this->custom_fields_str) {
            $extra_json['custom_fields'] = $this->custom_fields_str;
        }
        // BAEQ-3802
        foreach (['left_corrected_vision', 'right_corrected_vision', 'left_iop', 'right_iop', 'right_anterior_result', 'left_anterior_result'] as $column) {
            if (trim($request->REQUEST[$column])) {
                $extra_json[$column] = trim($request->REQUEST[$column]);
            }
        }
        $mv_score = 0;
        $has_mv = 0;
        if ($this->custom_fields) {
            foreach ($this->custom_fields as $id => $value) {
                if (strpos('pre' . $id, 'mv') && $this->age <= 18) {
                    if (empty($value)) {
                        $this->setView(10212, gettext('请输入所有标识有“*”的项目。'), []);
                        return false;
                    }
                    $has_mv = 1;
                    $mv_score += MacularVisualImpairment::handleQuestionnaireAnswer($id, $value);
                    if (is_array($value)) {
                        $extra_json[$id] = implode(',', $value);
                    } else {
                        $extra_json[$id] = $value;
                    }
                } else {
                    if (is_array($value)) {
                        $extra_json['custom_field_' . $id] = implode(',', $value);
                    } else {
                        $extra_json['custom_field_' . $id] = $value;
                    }
                }
            }
            if ($has_mv) {
                $extra_json['mv_score'] = $mv_score;
            }
        }
        //BAEQ-3105 针对特殊机构做信息收集功能，建立Airdocer档案集
        if (isset($request['physicalCondition']) && $request['physicalCondition']) {
            $extra_json['physicalCondition'] = $request['physicalCondition'];
        }

        if ($extra_json) {
            $params['extra_json'] = $extra_json;
        }
        if ($this->patient_code) {
            $pcode_item = $this->patient_code;
        } else {
            $pcode_item = PatientCode::getItemByPcode($pcode);
        }

        if (empty($pcode_item) && !$this->has_vcode) {
            $this->setView(10001, gettext('筛查码无效，请重新扫码获取。'), []);
            return false;
        }
        $org = Organizer::getOrganizerSelfById($params['org_id'])[0];
        if ($this->is_fd16) {
            $params['org_id'] = $this->fd16_user['org_id'] ? $this->fd16_user['org_id'] : $params['org_id'];
            if (
                strpos($this->openid, WX_OPENID_PREFIX) === 0
                && $this->fd16_user['config']['pay_config']
                && is_numeric($this->fd16_user['config']['pay_config'])
                && bccomp($this->fd16_user['config']['pay_config'], 0.01, 2) >= 0
                && !in_array($this->fd16_user['org']['config']['business_line'], [3, 4]) // ICVD和MV不走以下流程
            ) {
                if (!$pcode_item['user_id']) {
                    $this->setView(10011, gettext('筛查码无效，请重新扫码获取。'), []);
                    return false;
                }
                // $check_order = CheckOrder::checkExistByPcode($pcode_item['pcode']);
                // if (!$check_order || $check_order[0]['status'] == 0) {
                //     $this->setView(10012, '未支付成功，请重新扫码支付。', []);
                //     return false;
                // }
            }
        }

        $this->is_new_wechat = $pcode_item['new_wechat'];
        $this->openid = $pcode_item['openid'];
        $this->patient_id = $pcode_item['patient_id'];
        $old_patient = Patient::getPatientByUuid($this->pcode);

        $patient = new Patient();
        Logger::info($params, 'patient_info_add');
        if ($old_patient && $this->patient_id) {
            if ($this->patient_id != $old_patient['patient_id']) {
                $this->setView(10001, gettext('筛查码无效，请重新扫码获取。'), []);
                return false;
            } else {
                $patient_id = $params['patient_id'] = $old_patient['patient_id'];
                $patient->updatePatient($params);
            }
        } else {
            if (empty($old_patient)) {
                $patient_id = $patient->addPatient($params);
            } else {
                $patient_id = $params['patient_id'] = $old_patient['patient_id'];
                # 如果是从其他公众号过来的，且设置了medical_record_no
                if (array_key_exists('wxa', $old_patient['extra_json']) && $old_patient['medical_record_no']) {
                    $params['medical_record_no'] = $old_patient['medical_record_no'];
                    $params['insurance_text'] = $old_patient['insurance_text'];
                }
                $patient->updatePatient($params);
            }
        }
        $this->check_patient_id = $check_patient_id = $this->patient_id ? $this->patient_id : $patient_id;

        // 现已改为在start camera中写入此数据
        // // SME-144 近视预测数据需要写入到YoungCheckVisionInfo
        // if ($this->has_myopic_refraction) {
        //     $config_handler = new SMEConfig($this->org_id);
        //     if ($config_handler->getSMEOrgType() > 0) {
        //         $young_params = array_merge($params, ['age' => $this->age]);
        //         DBYoungCheckVisionInfoHelper::addYoungCheck($this->check_patient_id, $young_params);
        //     }
        // }

        // sync to SME AISP（神农）平台
        $this->sme_aisp_params = [];
        if (array_key_exists('org_id', $params) && intval($params['org_id']) > 1) {
            $params = array_replace($params, $data);
            if ($this->insurance_text && array_key_exists('customer_manager_id', $this->insurance_text)) {
                $params['agent_num'] = $this->insurance_text['customer_manager_id'];
            }
            $this->sme_aisp_params = $params;
        }

        // 健康管理账号创建
        // if ($params['org_id'] && $pcode_item['openid']) {
        //     $prefix = substr($pcode_item['openid'], 0, 5);
        //     if (in_array($prefix, [YTHEALTH_WX_OPENID_PREFIX, ICVD_WX_OPENID_PREFIX])) {
        //         $org = Organizer::getOrganizerSelfById($params['org_id'])[0];
        //         if ($org && $org['customer_id'] == 27) {
        //             $c_end_user = CEndUser::onCreatePatient($check_patient_id, ['openid' => $pcode_item['openid']]);
        //             if ($c_end_user) {
        //                 $c_end_user_params['patient_id'] = $check_patient_id;
        //                 $c_end_user_params['extra_json'] = $c_end_user;
        //                 $patient->updatePatient($c_end_user_params);
        //             }
        //         }
        //     }
        // }

        if ($this->has_vcode) {
            $this->expire_in = date('Y-m-d H:i:s', strtotime('+1days'));
            $this->vcode = VerificationCode::allocateCode($params['org_id'], $check_patient_id, $this->expire_in);
            $this->setView(0, gettext('获取福利码成功'), ['h5' => $this->vcode, 'miniprogram' => $this->vcode]);
            return true;
        }
        $check = CheckInfo::getLatestCheckInfoByPatientId([$check_patient_id]);
        if ($check) {
            $check_id = $check[0]['check_id'];
            CheckInfoExtra::updateInfo(['check_id' => $check_id, 'medical_history' => $this->medical_history]);
        }

        if ($this->is_fd16) {
            $show_fd16_video_str = intval($this->fd16_user['org']['config']['show_fd16_video']) === 1 ? '&show_fd16_video=1' : '';
            $show_fd16_qrcode_str = intval($this->fd16_user['org']['config']['show_fd16_qrcode']) === 1 || $this->camera['work_mode'] == 4 ? '&show_fd16_qrcode=1' : '';
            $hxt_plus_agent_str = intval($this->fd16_user['org']['config']['hxt_plus_agent']) === 1 ? '&hxt_plus_agent=1' : '';
            $work_mode_str = '&work_mode=' . $this->camera['work_mode'];
            $substr6Sn = '&substr6Sn=' . substr($this->plain_sn, -6);
            if ($this->pa_model == 3) {
                $work_mode_str = '&work_mode=4&pa_model=3';
            } elseif ($this->manniu_id) {
                $work_mode_str = '&work_mode=4&manniu=1';
            }
            // 小相机跳转到相机启动页
            //
            $params = '?en_openid=' . urlencode(Xcrypt::encrypt($this->openid))
                . '&pcode=' . urlencode($pcode)
                . '&sn=' . $this->sn
                . '&ts=' . time()
                . "&is_yingtong="
                . $this->is_yingtong
                . "&name=" . urlencode($this->name)
                . '&od_diopter=' . $this->od_diopter
                . '&os_diopter=' . $this->os_diopter
                . $show_fd16_video_str
                . $show_fd16_qrcode_str
                . $hxt_plus_agent_str
                . $work_mode_str
                . $substr6Sn;
            $open_payment = DBOrganizerHelper::is_third_party_pay($org);
            $check_order = CheckOrder::checkExistByPcode($pcode);
            if (count($check_order) > 1) {
                $this->setView(10011, gettext('筛查码无效，请重新扫码获取。'), []);
                return false;
            }
            $check_order = $check_order[0];
            Logger::error([
                'org_id' => $this->org_id, 'config_type' => gettype($org['config']), 'org_config' => $org['config'],
                'need_pay' => $need_pay, 'check_order' => $check_order
            ], 'patient_info');
            # 开启支付且未支付
            $need_pay = ($open_payment and !CheckOrder::isPay($check_order));
            if ($need_pay) {
                $this->fd16_start_url = 'pages/payAndCheck/fd16PayAndCheck' . $params;
            } else {
                $this->fd16_start_url = EYE_DOMAIN_HTTPS_PE . 'fd16/start' . $params;
            }

            if (getenv('LANG')) { //增加语言参数
                $this->fd16_start_url .= '&language=' . getenv('LANG');
            }
            if (empty($pcode_item['patient_id'])) {
                PatientCode::updatePatientIdInfo($pcode, $patient_id);
            }
            RedisPcodeImgUrl::setCache($pcode . '_fd16_url', $this->fd16_start_url);
            CheckLog::addLogInfo(0, 'patient_info_add_fd16', ['data' => ['params' => $params, 'openid' => $this->openid, 'sn' => $this->plain_sn, 'pcode' => $pcode]], 0, '', $pcode);
            $this->setView(0, 'fd16', [
                'h5' => $this->fd16_start_url,
                'miniprogram' => WXUtil::h5Url2Miniprogram($this->fd16_start_url),
                'need_pay' => $need_pay
            ]);
            return true;
        } else {
            // 大相机，跳转到筛查码
            $this->img_url = PatientCode::generateScreenImage($pcode, $name, $this->org_id, $this->request->REQUEST['hxt_plus_agent'], $this->openid);
            if ($this->img_url && empty($pcode_item['patient_id'])) {
                // BAEQ-3130
                $data = ['id' => $pcode_item['id'], 'patient_id' => $patient_id];
                if ($this->tiyan_code) {
                    if (!$this->openid) {
                        $this->openid = RedisCache::getCache('pcode_openid_' . trim($this->request->REQUEST['pcode']));
                    }
                    $data['openid'] = $this->openid;
                }
                PatientCode::updatePatientCode($data);
                CheckLog::addLogInfo(0, 'patient_info_add_big', ['data' => ['params' => $params, 'openid' => $this->openid, 'pcode' => $pcode]], 0, '', $pcode);
                $this->setView(0, gettext('登记信息成功'), ['h5' => $this->img_url, 'miniprogram' => $this->img_url]);
                return true;
            }
        }

        $this->setView(10009, gettext('登记信息失败'), []);
        return false;
    }

    private function updateAISP($org_id, $params)
    {
        $config_handler = new SMEConfig($org_id);
        if ($config_handler->aispRegistered()) {
            $ret = $config_handler->updatePatientInfo($params);
            return $ret;
        }
        return true;
    }

    private function _init()
    {
        $request = $this->request->REQUEST;
        // if (empty(trim($request['name']))) {
        //     $this->setView(10003, '请输入姓名', []);
        //     return FALSE;
        // }
        $this->register_required_options = htmlspecialchars_decode($request['register_required_options']);
        $this->name = trim($request['name']);
        if (trim($request['pcode'])) {
            $this->pcode = trim($request['pcode']);
            if (strlen(trim($request['pcode'])) >= 32) {
                $this->pcode = Xcrypt::decrypt(trim($request['pcode']));
            }
        }
        // BAEQ-3130
        $this->tiyan_code = trim($request['tiyan_code']);
        $this->is_tiyan = intval($request['is_tiyan']);
        // BAEQ-3130
        if ($this->tiyan_code) {
            if (strtoupper(substr($this->tiyan_code, 0, 2)) != 'TY') {
                $this->setView(10201, '福利码不符合规范，请确认福利码是否正确。', []);
                return false;
            }
            $this->patient_code = PatientCode::getItemByPcode($this->tiyan_code);
            if (!$this->patient_code) {
                $this->setView(10202, '福利码不存在，请确认福利码是否正确。', []);
                return false;
            }
            if ($this->patient_code['check_id']) {
                $this->setView(10203, '福利码已被使用，请联系对接人申请一个新的福利码。', []);
                return false;
            }
            $this->pcode = $this->tiyan_code;
        }
        $this->sn = trim($request['sn']);
        $this->phone = trim($request['phone']);
        $this->org_id = trim($request['org_id']) != "5001" ? trim($request['org_id']) : 0;
        $this->pa_model = trim($request['pa_model']);
        $this->en_openid = trim($request['en_openid']);
        $this->industry = trim($request['industry']);
        if ($this->pa_model) {
            $this->phone = Xcrypt::aes_decrypt(Xcrypt::decrypt($this->en_openid));
            if ($this->pa_model == 2) {
                $this->has_vcode = 'vcode_' . $this->phone . "_" . date('YmdHis');
            }
        }
        if ($this->org_id == ManniuHandler::MANNIU_ORG_ID[ENV]) {
            $prefix_manniu_id = Xcrypt::decrypt($this->en_openid);
            if ($prefix_manniu_id && strpos('pre' . $prefix_manniu_id, 'manniu_')) {
                $this->manniu_id = str_replace('manniu_', '', $prefix_manniu_id);
            }
        }
        $register_login_token = RedisCache::getCache($this->phone . $this->pcode . $this->sn, 'register_login_');
        $this->token = trim($request['token']);
        // BAEQ-3130
        if (!$this->tiyan_code) {
            if ($register_login_token && $register_login_token != $this->token) {
                $this->setView(10001, gettext('权限不够'), []);
                return false;
            } elseif ($register_login_token || $this->token) {
                $register_login_token = $register_login_token ? $register_login_token : $this->token;
                $register_data = RedisSession::getSession($register_login_token);
                if ($register_data['phone'] != $this->phone) {
                    $this->setView(10002, gettext('权限不够'), []);
                    return false;
                }
            }
        }
        $this->is_fd16 = 0;
        if (!empty(trim($request['is_fd16']))) {
            $this->is_fd16 = trim($request['is_fd16']);
        }
        $org_id = $this->org_id;
        if ($this->is_fd16) {
            if (!empty($this->sn)) {
                $this->camera = CameraHandler::getCameraBySN($this->sn);
                $this->plain_sn = $this->camera['sn'];
                $this->user_id = $this->camera['user_id'];
                $this->user_obj = new User();
                $this->fd16_user = $this->user_obj->getUserById($this->user_id);
                $this->org_id = $this->fd16_user['org_id'] ? $this->fd16_user['org_id'] : $this->org_id;
            }
        }
        if ($this->sn) {
            $bisheng_camera = CameraHandler::getCameraBySNOrMd5($this->sn);
            if ($bisheng_camera['sn']) {
                $BishengUtil = new BishengUtil();
                $bisheng_config = $BishengUtil->getConfigByDevice($bisheng_camera['sn']);
            }
        }
        $obj = new Organizer();
        $org = $obj->getOrganizerById($this->org_id);
        if (gettype($org['config']) == "string") {
            $org['config'] = json_decode($org['config'], true);
        }
        if (isset($org['config']['check_quantify_config']) && gettype($org['config']['check_quantify_config']) == "string") {
            $org = Organizer::decodeConfig($org);
        }

        $this->has_myopic_refraction = Organizer::hasMyopicRefraction($org);

        if (isset($bisheng_config['register_required_options'])) {
            $register_required_options = $bisheng_config['register_required_options'];
            if (!is_array($register_required_options) && !empty($register_required_options)) {
                $register_required_options = json_decode($register_required_options, TRUE);
            }
        } elseif ($org['config']['register_required_options']) {
            $register_required_options = $org['config']['register_required_options'];
            $register_required_options = !empty($register_required_options) ? json_decode($register_required_options, TRUE) : [];
        }

        $this->register_required_options = $register_required_options ?? [];
        $has_mv_qs = false;
        if (!empty($bisheng_config)) {
            if (!empty($bisheng_config['health_risk_item_content'])) {
                $health_risk_item_content = explode(',', $bisheng_config['health_risk_item_content']);
                $has_mv_qs = in_array('macular_visual_impairment', $health_risk_item_content);
            }
        } elseif ($org['config']['business_line'] == 4 || !empty($org['new_risk']['custom_sort']) && in_array('macularVisualImpairment', $org['new_risk']['custom_sort'])) {
            $has_mv_qs = true;
        }

        // 屈光预测需要球镜度数
        if ($this->has_myopic_refraction) {
            $has_mv_qs = true;
        }

        if ($has_mv_qs) {
            //球镜
            if (isset($request['right_sphere'])) {
                $this->right_sphere = trim($request['right_sphere']);
            }
            if (isset($request['left_sphere'])) {
                $this->left_sphere = trim($request['left_sphere']);
            }
            //柱镜
            if (isset($request['right_column_mirror'])) {
                $this->right_column_mirror = trim($request['right_column_mirror']);
            }
            if (isset($request['left_column_mirror'])) {
                $this->left_column_mirror = trim($request['left_column_mirror']);
            }

            $right_sphere = array("key" => "right_sphere", "label" => "球镜（右）", "value" => 1);
            $left_sphere = array("key" => "left_sphere", "label" => "球镜（左）", "value" => 1);
            $right_column_mirror = array("key" => "right_column_mirror", "label" => "柱镜（右）", "value" => 1);
            $left_column_mirror = array("key" => "left_column_mirror", "label" => "柱镜（左）", "value" => 1);
            array_push($this->register_required_options, $right_sphere, $left_sphere, $right_column_mirror, $left_column_mirror);
            if (isset($request['right_sphere']) && !$this->checkSphere($this->right_sphere) && Organizer::isRequired('right_sphere', $this->register_required_options)) {
                return false;
            }
            if (isset($request['left_sphere']) && !$this->checkSphere($this->left_sphere) && Organizer::isRequired('left_sphere', $this->register_required_options)) {
                return false;
            }
            if (isset($request['right_column_mirror']) && !$this->checkColumnMirror($this->right_column_mirror) && Organizer::isRequired('right_column_mirror', $this->register_required_options)) {
                return false;
            }
            if (isset($request['left_column_mirror']) && !$this->checkColumnMirror($this->left_column_mirror) && Organizer::isRequired('left_column_mirror', $this->register_required_options)) {
                return false;
            }
        }

        $this->height = trim($request['height']);
        $this->weight = trim($request['weight']);
        if (isset($request['height']) && !$this->checkHeight($this->height) && Organizer::isRequired('height', $this->register_required_options)) {
            return false;
        }
        if (isset($request['weight']) && !$this->checkWeight($this->weight) && Organizer::isRequired('weight', $this->register_required_options)) {
            return false;
        }
        $this->client = trim($request['client']);
        $is_women_child = 0;
        if ($org_id && in_array($org_id, [40104, 40143])) {
            $is_women_child = 1;
        }
        if ($this->phone && (!$is_women_child && $this->client !== 'miniprogram' && !$this->pa_model) || $this->manniu_id) {
            $code = $request['code'];
            $server_code = RedisSession::get('wechat' . md5($this->phone));
            $door_code = date('dm', time() - 86400);
            if (empty($code) || ($code != $server_code && $code != $door_code)) {
                $this->setView(10003, gettext('短信验证码不正确'), []);
                return false;
            }
        }
        $this->birthday = $request['birthday'];
        if (!$this->birthday && $request['age'] > 0) {
            $this->birthday = date('Y-m-d', time() - $request['age'] * 31622400);
            $this->age = $request['age'];
        } else {
            $this->age = Patient::getAge($this->birthday);
        }
        $this->id_number = isset($request['id_number']) ? trim($request['id_number']) : '';
        if ($this->id_number && !Utilities::is_idcard($this->id_number)) {
            $this->setView(10001, gettext('身份证号不正确'), '');
            return false;
        } else if ($this->id_number && Utilities::is_idcard($this->id_number)) {
            $this->birthday = Utilities::getBirthdayFromIDcard($this->id_number);
            $this->gender = Utilities::getGenderfromIDCard($this->id_number);
            $this->age = Patient::getAge($this->birthday);
        }
        if (empty($this->id_number)) {
            $this->gender = $request['gender'];
            if (empty($this->gender) || !in_array($this->gender, [0, 1, 2])) {
                $this->setView(10003, gettext('性别值不符合规范'), []);
                return false;
            }
        }
        $this->insurance_text = [];
        if (trim($request['agent_num']) && $request['agent_num'] != 'undefined') {
            if (in_array($org_id, ZhongyingHandler::ORG_ID[ENV])) {
                $check_is_agent = ZhongyingHandler::checkIsAgent($request['agent_num']);
                if ($check_is_agent['error_code'] != 0) {
                    $this->setView(10003, $check_is_agent['message'], []);
                    return false;
                }
            }
            $this->insurance_text['customer_manager_id'] = trim($request['agent_num']);
        }

        $this->insurance = 0;
        $this->insurance = trim($request->REQUEST['ins_v2']);
        if (!empty(trim($request['insurance']))) {
            $this->insurance = trim($request['insurance']);
        }

        if (!empty(trim($request['en_openid'])) && trim($request['en_openid']) != 'undefined') {
            $en_openid = rawurldecode(str_replace(' ', '+', trim($request['en_openid'])));
            $this->openid = Xcrypt::decrypt($en_openid);
        }
        $org_id = $this->org_id;
        $this->is_yingtong = trim($request['is_yingtong']);

        if (isset($request['company']) && Organizer::isRequired('company', $this->register_required_options)) {
            $this->company = trim($request['company']);
        }

        $this->od_diopter = isset($request['od_diopter']) ? trim($request['od_diopter']) : '-1';
        $this->os_diopter = isset($request['os_diopter']) ? trim($request['os_diopter']) : '-1';

        if ($this->pcode) {
            $diopter = [
                'OD' => $this->od_diopter,
                'OS' => $this->os_diopter,
            ];
            RedisCache::setCache($this->pcode, serialize($diopter), CameraHandler::PCODE_PREFIX, 86400 * 30);
        }
        $this->ssy = isset($request['systolicPressure']) ? intval(trim($request['systolicPressure'])) : '';
        $this->szy = isset($request['diastolicPressure']) ? intval(trim($request['diastolicPressure'])) : '';
        $this->gestational = isset($request['gestationalWeek']) ? intval(trim($request['gestationalWeek'])) : '';
        $this->medical_record_no = '';
        if ($request['medical_record_no']) {
            $this->medical_record_no = trim($request['medical_record_no']);
            $this->third_id_name = trim($request['third_id_name']) ? trim($request['third_id_name']) : '';
            if ($this->third_id_name == '手机号' && !Utilities::isPhone($this->medical_record_no) && Organizer::isRequired('phone', $this->register_required_options)) {
                $this->setView(10001, gettext('请输入正确的手机号'), []);
                return FALSE;
            }
            if ($this->third_id_name == '身份证' && !Utilities::is_idcard($this->medical_record_no)) {
                $this->setView(10001, gettext('请输入正确的身份证号码。'), []);
                return FALSE;
            }
            if ($this->third_id_name == '身份证' && Utilities::is_idcard($this->medical_record_no)) {
                $this->id_number = $this->medical_record_no;
                $this->medical_record_no = '';
            }
        }
        // BAEQ-3210 蓝颐
        if (!$this->medical_record_no) {
            $this->medical_record_no = RedisCache::getCache('pcode_2_medical_record_no_' . $this->pcode);
        }
        $this->complained = isset($request['complained']) ? trim($request['complained']) : '';
        $this->medical_history = isset($request['medical_history']) ? trim($request['medical_history']) : '';
        $this->medical_history = str_replace('undefined', '', $this->medical_history);
        $this->medical_history = trim($this->medical_history, ',');
        if ($this->medical_history) {
            $medial_array = array_unique(explode(',', $this->medical_history));
            $this->medical_history = implode(',', $medial_array);
        }

        if ($request['custom_fields']) {
            $request['custom_fields'] = str_replace('&quot;', '"', $request['custom_fields']);
            $this->custom_fields_str = $request['custom_fields'];
            $custom_fields = json_decode($request['custom_fields'], 1);
            if ($custom_fields) {
                $this->custom_fields = $custom_fields;
            }
        }
        if ($org_id && $this->client == 'miniprogram') {
            $custom_fields = CustomFields::getCustomFieldsGroupByType($org_id);
            foreach ($custom_fields as $type_key => $field_type_items) {
                $action = $type_key == 'input' ? '输入' : '选择';
                if ($field_type_items) {
                    foreach ($field_type_items as $custom_field) {
                        if ($custom_field['required'] && !$this->custom_fields[$custom_field['id']]) {
                            $this->setView(10003, gettext('请' . $action) . $custom_field['title'], []);
                            return FALSE;
                        }
                    }
                }
            }
        }

        return true;
    }

    private function checkWeight($weight)
    {
        if ($weight < 10 || $weight > 300) {
            $this->setView(10003, gettext('体重不符合规范'), []);
            return false;
        }

        return true;
    }

    private function checkHeight($height)
    {
        if ($height < 80 || $height > 250) {
            $this->setView(10003, gettext('身高不符合规范'), []);
            return false;
        }

        return true;
    }


    private function checkSphere($sphere)
    {
        if (!is_numeric($sphere) || $sphere < -30 || $sphere > 30) {
            $this->setView(10003, gettext('球镜不符合规范'), []);
            return false;
        }
        return true;
    }

    private function checkColumnMirror($columnMirror)
    {
        if (!is_numeric($columnMirror) || $columnMirror < -10 || $columnMirror > 10) {
            $this->setView(10003, gettext('柱镜不符合规范'), []);
            return false;
        }
        return true;
    }

    public function asyncJob()
    {
        // sync to SME aisp
        if ($this->sme_aisp_params) {
            $this->updateAISP(intval($this->sme_aisp_params['org_id']), $this->sme_aisp_params);
        }

        if (
            $this->view['error_code'] == 0 &&
            substr($this->pcode, 0, 4) === ICVD_PCODE_PREFIX
            && substr($this->openid, 0, 5) === ICVD_WX_OPENID_PREFIX
            && $this->img_url
        ) {
            WechatUserCheck::sendImageByOpenId($this->name, $this->openid, $this->img_url, $this->pcode, 2, 1);
        } elseif ($this->view['error_code'] == 0) {
            if ($this->org_id == PA_ZY_ORG_ID || $this->fd16_user['org_id'] == PA_ZY_ORG_ID && PA_SWITCH == 1) {
                PAUtil::pushPatientInfo($this->check_patient_id);
            } elseif ($this->org_id == PA_APP_ORG_ID || $this->fd16_user['org_id'] == PA_APP_ORG_ID && PA_APP_SWITCH == 1) {
                PAUtil::pushPatientInfo($this->check_patient_id);
            } elseif ($this->org_id == ManniuHandler::MANNIU_ORG_ID[ENV] && $this->manniu_id) {
                ManniuHandler::pushPatientInfo($this->check_patient_id);
            }
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
