<?php
/**
 * Created by PhpStorm.
 * User: 翁劲
 * Date: 2018/11/27
 * Time: 15:21
 */

namespace Air\Modules\Wechat;
use Air\Package\Patient\Patient;
use Air\Package\User\PatientCode;
use Air\Package\Wechat\WechatUserCheck;
use Air\Libs\Xcrypt;
use Air\Package\Barcode\Barcode;
use Air\Package\Checklist\CheckLog;
use Air\Package\Checklist\Helper\RedisImageUrl;
use Air\Package\Checklist\Image;
use \Air\Package\Checklist\QiniuHandler;
use Air\Package\Fd16\CameraHandler;
use Air\Package\Smb\SnPcode;
use Air\Package\User\User;
use Air\Package\Wechat\WXUtil;
use Air\Package\Wechat\helper\RedisPcodeImgUrl;
use \Air\Package\Wechat\Helper\RedisGeneralReport;

class Patient_info_add_before extends \Air\Libs\Controller
{
    public $must_login = false;
    public $general = 0;

    public function run()
    {
        if (!$this->_init()) {
            return FALSE;
        }
        $pobj = new Patient();
        $request = $this->request;
        $this->name = $name = trim($request->REQUEST['name']);
        $gender = $this->gender;
        $birthday = $this->birthday;
        $phone = $this->phone ? $this->phone : '';
        $address = $this->address ? $this->address : '';
        $data = [];
        $data['complained'] = trim($request->REQUEST['complained']);
        $other_complained = trim($request->REQUEST['other_complained']);
        if ($other_complained) {
            $data['complained'] = $data['complained']
                ? $data['complained'].";".$other_complained
                : $other_complained;
        }
        if ($this->insurance_text) {
            $insurance_text = json_encode($this->insurance_text, JSON_UNESCAPED_UNICODE);
        }
        $data['medical_history'] = trim($request->REQUEST['medical_history']);
        $other_history = trim($request->REQUEST['other_history']);
        $data['other_history'] = $other_history;
        $extra_json = [];
        foreach ([
          'ssy', // 收缩压
          'szy', // 舒张压
          'bvid', // ID
          'smoke_history', // 是否吸烟
          // qinghai
          'nation', // 民族
          'area', // 居住地区
          'residence_time', // 居住时间
          'accommodation', // 居住类型
          'waistline', // 腰围
          'blood_oxygen', // 血氧
          'cholesterin', // 总胆固醇
          'high_cholesterin', // 高密度脂蛋白胆固醇
          // xizang
          'neck_circumference', // 脖围
          'etco2', // ETCO2
          'hemoglobin', // 血红蛋白
          'low_holesterol', // 低密度脂蛋白胆固醇
          'blood_sugar', // 血糖
          'hba1c', // 糖化血红蛋白
          'hcy', // 同型半胱氨酸项目
          'keshi', // 科室
          'smoke_year', // 累计抽烟多少年
          'smoke_count', // 平均每天多少包
          'diagnosis_results', //诊断结果
          'neck_cta', // 颈动脉超声/投颈部CTA
          'mac_os_vs', //MAC左眼vs
          'mac_os_vd', //MAC左眼vd
          'mac_od_vs', //MAC右眼vs
          'mac_od_vd', //MAC右眼vd
          'right_anterior_result', // BAEQ-3802 眼前节结果
          'left_anterior_result',  // BAEQ-3802 眼前节结果
          'left_corrected_vision', 'right_corrected_vision', 'left_iop', 'right_iop',
          ] as $col) {
            if (isset($request->REQUEST[$col])) {
                $extra_json[$col] = trim($request->REQUEST[$col]);
            }
        }
        $params = [
            'uuid' => $this->code,
            'birthday' => $birthday,
            'gender' => $gender,
            'id_number_crypt' => $this->id_number,
            "id_type" => $this->id_type,
            'name' => $name,
            'status' => 1,
            'extra' => $data ? $data : '',
            'phone' => $phone,
            'address' => $address,
            'insurance_text' => $insurance_text ? $insurance_text : '',
            'extra_json' => $extra_json,
        ];
        if ($this->weight) {
            $params['weight'] = $this->weight;
        }
        if ($this->height) {
            $params['height'] = $this->height;
        }
        $old_patient = Patient::getPatientByUuid($this->code);
        $old_code = PatientCode::getItemByPcode($this->code);
        $this->is_new_wechat = $is_new_wechat = $old_code['new_wechat'];
        if ($old_patient) {
            $prefix = substr($this->code, 0, 4);
            list($id, $params['uuid']) = PatientCode::initCode($this->open_id, $prefix, 1, 0, $is_new_wechat);
            $this->code = $params['uuid'];
        }
        list($pid, $old_patient) = $pobj->handleIkangPatient($params, 1);
        if ($pid && $this->is_fd16) {
            $camera = CameraHandler::getCameraBySN($this->sn);
            $user_id = $camera['user_id'];
            $user_obj = new User();
            $this->fd16_user = $user_obj->getUserById($user_id);
            $show_fd16_video_str = intval($this->fd16_user['org']['config']['show_fd16_video']) === 1 ? '&show_fd16_video=1' : '';
            $show_fd16_qrcode_str = intval($this->fd16_user['org']['config']['show_fd16_qrcode']) === 1 || $camera['work_mode'] == 4 ? '&show_fd16_qrcode=1' : '';
            $hxt_plus_agent_str = intval($this->fd16_user['org']['config']['hxt_plus_agent']) === 1 ? '&hxt_plus_agent=1' : '';
            $work_mode_str = '&work_mode=' . $camera['work_mode'];
            // 小相机跳转到相机启动页
            $this->fd16_start_url = EYE_DOMAIN_HTTPS_PE . 'fd16/start?en_openid=' . urlencode(Xcrypt::encrypt($this->openid))
                . '&pcode=' . urlencode($this->code) . '&sn=' . $this->sn . '&ts=' . time() . "&is_yingtong=&name=" . urlencode($this->name)
                . '&od_diopter=-1&os_diopter=-1' . $show_fd16_video_str . $show_fd16_qrcode_str . $hxt_plus_agent_str . $work_mode_str;
            PatientCode::updatePatientIdInfo($this->code, $pid);
            RedisPcodeImgUrl::setCache($this->code . '_fd16_url', $this->fd16_start_url);

            CheckLog::addLogInfo(0, 'patient_info_add_before_fd16', ['data' => ['params' => $params, 'sn' => $camera['sn'], 'openid' => $this->openid]], $user_id, '', $this->code);
            $this->setView(0, 'fd16', $this->fd16_start_url);
            return true;
        } elseif ($pid) {
            $push_type = $this->insurance;
            $qrcode_img_file = '/tmp/qr_' . $this->code . '.png';
            $barcode_img_file = '/tmp/bar_' . $this->code .  '.png';
            Barcode::generateLocalQrCodeImage($this->code, $qrcode_img_file);
            Barcode::generateLocalBarcodeImage($this->code, $barcode_img_file);
            $temp_img_file = '/tmp/temp_' . $this->code . '.png';
            $pc_ret = PatientCode::updatePatientIdInfo($this->code, $pid);
            $is_ytsm = substr($this->code, 0, 4) == ICVD_PCODE_PREFIX;
            $is_third = intval($this->frm == 'yuanhe');
            //中翔定制AK-926
            $this->zx && $is_third = 2;
            WXUtil::generateScreenImage(['name' => $params['name'], 'qrcode' => $qrcode_img_file, 'barcode' => $barcode_img_file], $this->code, $temp_img_file, $push_type, $is_third, 0 , $is_ytsm);
            $md5 = md5(file_get_contents($temp_img_file));
            //zj $ret_img = QiniuHandler::uploadImage($temp_img_file, $md5  . '.png');
            $ret_img = Image::uploadImage($temp_img_file, $md5  . '.png');
            if ($ret_img) {
                //zj $this->img_url = IMG_DOMAIN . $ret_img[0]['key'];
                $this->img_url = $ret_img;
                //zj if (defined('IMG_SWITCH') && IMG_SWITCH) {
                //     if (defined('IMG_DOMAIN_NEW_HTTPS')) {
                //         $this->img_url = \Phplib\Image\ImageSign::getSignedUrl(IMG_DOMAIN_NEW_HTTPS . $ret_img[0]['key'], 8640000);
                //     }
                //     else {
                //         $this->img_url = \Phplib\Image\ImageSign::getSignedUrl(IMG_DOMAIN_NEW . $ret_img[0]['key'], 8640000);
                //     }
                // }
                $this->img_url = RedisImageUrl::signedUrl($ret_img, 8640000);
            } else {
                \Phplib\Tools\Logger::error(['upload_wechat_image_to_qiniu_filed', $this->code, $this->open_id, $temp_img_file], 'wechat_send_msg_error');
                return false;
            }
            RedisPcodeImgUrl::setCache($this->code, $this->img_url);
            //for健维
            RedisPcodeImgUrl::setCache($this->open_id, $this->img_url);
            if ($this->zx) {
                RedisPcodeImgUrl::setCache($this->open_id . '_zx', $this->img_url);
            }
            $ins_v2 = (int) $request->REQUEST['ins_v2'];
            if (empty($request->REQUEST['sn']) && !$is_ytsm) {
                WechatUserCheck::sendImageByOpenId($this->name, $this->open_id, $this->img_url, $this->code, $is_new_wechat);
            } else if ($is_ytsm) {
                WechatUserCheck::sendImageByOpenId($this->name, $this->open_id, $this->img_url, $this->code, $is_new_wechat, 1);
            }
            else {
                RedisPcodeImgUrl::setCache($this->code . "_name", $this->name);
                RedisPcodeImgUrl::setCache($this->code . "_openid", $this->open_id);
            }
            unlink($barcode_img_file);
            unlink($qrcode_img_file);
            unlink($temp_img_file);

            CheckLog::addLogInfo(0, 'patient_info_add_before_qr', ['data' => ['params' => $params, 'openid' => $this->openid]], 0, '', $this->code);
            $this->setView(0, $this->code, $this->img_url);
            return;
        }
        $this->setView(10009, '添加信息失败', []);
        return false;
    }


    private function _init()
    {
        $request = $this->request;
        if (empty(trim($request->REQUEST['name']))) {
            $this->setView(10003, '请输入姓名', []);
            return FALSE;
        }
        $this->frm = trim($request->REQUEST['frm']);
        if ($this->frm == 'zx') {
            $this->zx = 1;
        }
        $this->phone = trim($request->REQUEST['phone']);
        $this->weight = $request->REQUEST['weight'];
        $this->height = intval($request->REQUEST['height']);
        $ins_v2 = trim($request->REQUEST['ins_v2']);
        //健维api
        if ($this->frm && in_array($this->frm, ['general', 'yuanhe', 'zx', 'chronic'])) {
            //$this->weight = floor($request->REQUEST['weight'] / 2);
            $this->general = 1;
            $this->open_id = $this->token;
        }
        else {
            $this->open_id = Xcrypt::decrypt($request->REQUEST['en_open_id']);
            \Phplib\Tools\Logger::error([$request->REQUEST['en_open_id'], $this->open_id], 'open_id_debug');
        }
        //if (!$this->general && empty($this->open_id) && !strpos($_SERVER['HTTP_REFERER'], 'xzregister')) {
        if (!$this->general && empty($this->open_id)) {
            $this->setView(10003, 'open_id 不符合规范', []);
            return FALSE;
        }

        $this->bv = (int) $request->REQUEST['bv'];
        if (!$this->bv && empty($this->phone)){
            $this->setView(10003, '请输入手机号码', []);
            return FALSE;
        }
        if (!$this->bv && !\Air\Libs\Base\Utilities::isPhone($this->phone) && !\Air\Libs\Base\Utilities::isTelephone($this->phone)) {
            $this->setView($this->error_code_prefix . '03', '请输入正确的电话号码', []);
            return FALSE;
        }
        if (!$this->bv && empty(trim($request->REQUEST['insurance']))) {
            $this->sms_code = trim($request->REQUEST['sms_code']);
            if (empty($this->sms_code)) {
                $this->setView(100013, '请输入短信验证码', []);
                return FALSE;
            }
            $sms_code_server = \Air\Package\Session\Helper\RedisSession::get('wechat' . md5($this->phone));
            $door = substr($this->phone, 1, 2) . substr($this->phone, 8, 2);
            if ($this->sms_code != $sms_code_server && $this->sms_code != $door) {
                $this->setView(100023, '短信验证码验证失败，请确认验证码是否正确', []);
                return FALSE;
            }
            if ($this->general) {
                RedisGeneralReport::setCache($this->open_id . '2phone', $this->phone);
            }
        }
        if (empty(trim($request->REQUEST['pcode']))) {
            $not_push = 1;
            if ($this->zx) {
                $prefix = ZX_PCODE_PREFIX;
                $new_wechat = -1;
            }
            // AK-1203 chronic
            elseif ($this->general && $this->frm == 'chronic') {
                $prefix = '8996';
                $new_wechat = -1;
            }
            elseif ($this->general) {
                $prefix = '8989';
                $new_wechat = -1;
            }
            else {
                $prefix = '8991';
                $old_pitem = PatientCode::getItemsByOpenid($this->open_id, 0);
                if (substr($old_pitem['pcode'], 0, 2) == '89') {
                    $prefix = substr($old_pitem['pcode'], 0, 4);
                }
                $new_wechat = $old_pitem ? $old_pitem['new_wechat'] : 0;
            }
            list($id, $this->code) = PatientCode::initCode($this->open_id, $prefix, $not_push, 0, $new_wechat);
        }
        else {
            $this->code = trim($request->REQUEST['pcode']);
        }
        $this->id_type = $request->REQUEST['type'];
        $this->id_number = $idcard = trim($request->REQUEST['id_number']);
        $request->REQUEST['noid'] = (int) $request->REQUEST['noid'];
        $this->sn = trim($request->REQUEST['sn']);
        $this->is_fd16 = 0;
        if (!empty(trim($request->REQUEST['is_fd16']))) {
            $this->is_fd16 = trim($request->REQUEST['is_fd16']);
        }
        $this->insurance = 0;
        $this->insurance = trim($request->REQUEST['ins_v2']);
        if (empty($request->REQUEST['noid']) && $this->insurance != 3) {
            if (empty(trim($request->REQUEST['id_number']))) {
                $this->setView(10003, '请输入证件号', []);
                return FALSE;
            }
            if ($this->id_type == 1) {
                if (!\Air\Libs\Base\Utilities::is_idcard(trim($request->REQUEST['id_number']))) {
                    $this->setView(10003, '请输入正确的身份证号', []);
                    return FALSE;
                }
                if (strlen($idcard) == 18) {
                    $ymd = substr($idcard, 6, 8);
                    if (substr($idcard, 6, 2) != '19' && substr($idcard, 6, 2) != '20') {
                        $ymd = '19' . substr($idcard, 8, 6);
                    }
                }
                elseif (strlen($idcard) == 15) {
                    $ymd = '19' . substr($idcard, 6, 6);
                }
                $this->birthday = $ymd;
                $this->gender = substr($idcard, -2, 1) % 2 ? '1' : '2'; //1为男 2为女
            }
        }
        if (!empty($request->REQUEST['noid']) || $this->id_type == 2 || $this->insurance == 3) {
            if (!empty($request->REQUEST['birthday'])) {
                $this->birthday = $request->REQUEST['birthday'];
            }
            if (!empty($request->REQUEST['birthday']) && !strtotime($request->REQUEST['birthday'])) {
                $this->setView($this->error_code_prefix . '06', '用户生日不符合规范!', []);
                return FALSE;
            }
            if (!$this->birthday && empty($request->REQUEST['age']) || $request->REQUEST['age'] < 0 || $request->REQUEST['age'] > 150) {
                $this->setView($this->error_code_prefix . '06', '用户年龄不符合规范!', []);
                return FALSE;
            }
            if (!$this->birthday) {
                $request->REQUEST['age'] > 0 && $this->birthday = date('Y-m-d', time() - $request->REQUEST['age'] * 31622400);
            }
            $this->gender = $request->REQUEST['gender'];
        }
        if (0 && substr($this->code, 0, 4) == '8993'){
            $this->address = trim($request->REQUEST['address']);
            if(empty($this->address)){
                $this->setView(10003, '请输入邮寄地址', []);
                return FALSE;
            }
        }
        $this->insurance = 0;
        $this->insurance = trim($request->REQUEST['ins_v2']);
        $this->insurance_text['subsidiary_company'] = trim($request->REQUEST['subsidiary_company']);
        $this->insurance_text['workplace'] = trim($request->REQUEST['workplace']);
        $this->insurance_text['customer_manager_name'] = trim($request->REQUEST['customer_manager_name']);
        $this->insurance_text['customer_manager_id'] = trim($request->REQUEST['customer_manager_id']);
        if (!empty(trim($request->REQUEST['insurance']))) {
            $this->insurance = trim($request->REQUEST['insurance']);
            $info = [];
            if (empty(trim($request->REQUEST['subsidiary_company']))) {
                $info[] = '支公司';
            }
            if (empty(trim($request->REQUEST['workplace']))) {
                $info[] = '职场';
            }
            if (empty(trim($request->REQUEST['customer_manager_name'])) || empty(trim($request->REQUEST['customer_manager_id']))) {
                $info[] = '客户经理';
            }
            if (!empty($info)) {
                $this->setView(10003, '请完善' . implode('、', $info) . '信息', []);
                return FALSE;
            }
        }
        if (!empty(trim($request->REQUEST['ins_v2']))){
            $this->insurance_text['customer_manager_id'] = trim($request->REQUEST['customer_manager_id']);
            if ($request->REQUEST['req_id'] && empty($this->insurance_text['customer_manager_id'])) {
                $this->setView(10003, '请输入业务员工号', []);
                return FALSE;
            }
        }
        if (!empty(trim($request->REQUEST['ins_v2'])) && $request->REQUEST['ins_v2'] == 3){
            if (empty($this->insurance_text['subsidiary_company'])) {
                $this->setView(10003, '请输入营业区', []);
                return FALSE;
            }
            if (empty($this->insurance_text['workplace'])) {
                $this->setView(10004, '请输入营业部', []);
                return FALSE;
            }
            if (empty($this->insurance_text['customer_manager_name'])) {
                $this->setView(10005, '请输入业务员姓名', []);
                return FALSE;
            }
        }
        if ($this->insurance != 3 && !$this->sn && !$this->bv && ($this->height < 50 || $this->height > 250)) {
            $this->setView(10003, '身高不符合规范，身高单位是厘米哦', []);
            return FALSE;
        }
        if (trim($request->REQUEST['ssy']) && $request->REQUEST['ssy'] > 300) {
            $this->setView(10003, '高压（收缩压）不符合规范', []);
            return FALSE;
        }
        if (trim($request->REQUEST['szy']) && $request->REQUEST['szy'] > 300) {
            $this->setView(10003, '低压（舒张压）不符合规范', []);
            return FALSE;
        }
        if ($this->insurance != 3 && !$this->sn && !$this->bv && ($this->weight < 10 || $this->weight > 300)) {
            $this->setView(10003, '体重不符合规范', []);
            return FALSE;
        }
        return true;
    }
    public function asyncJob()
    {
        if ($this->view['error_code'] == 0 && !empty($this->sn) && $this->is_fd16) {
            $camera = CameraHandler::getCameraBySN($this->sn);
            $plain_sn = $camera['sn'];
            $user_id = $camera['user_id'];
            SnPcode::createSnPcode(['pcode' => $this->code, 'sn' => $plain_sn, 'user_id' => $user_id]);
            if ($this->fd16_start_url) {
                WechatUserCheck::sendImageByOpenId($this->name, $this->openid, $this->fd16_start_url, $this->code, $this->is_new_wechat, 0, 1);
                RedisPcodeImgUrl::setCache($this->code . '_fd16_url', $this->fd16_start_url);
            }
        }
    }
}
