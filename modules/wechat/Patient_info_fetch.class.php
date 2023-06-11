<?php

namespace Air\Modules\Wechat;

use Air\Libs\Base\Utilities;
use \Air\Libs\Xcrypt;
use \Air\Package\Fd16\CameraHandler;
use Air\Package\Patient\Patient;
use \Air\Package\Wechat\helper\RedisPcodeImgUrl;
use \Air\Package\User\PatientCode;
use \Air\Package\User\User;
use Air\Package\Thirdparty\ThirdHandler;
use Air\Package\User\Organizer;
use Air\Package\Wechat\WechatScene;
use Air\Package\Wechat\WechatUserCheck;
use Phplib\Tools\Logger;

/**
 * 获取用户信息
 */
class Patient_info_fetch extends \Air\Libs\Controller
{
    private $data = [];
    public $runtime = 1;

    public function run()
    {
        if (!$this->_init()) {
            return FALSE;
        }
        $url = ThirdHandler::FETCH_API['taiping'][ENV];
        $user_info = Utilities::curl($url, $this->data, ['is_json' => 1, 'is_post' => 1]);
        if (empty($user_info)) {
            $user_info = Utilities::curl($url, $this->data, ['is_json' => 1, 'is_post' => 1]);
        }
        Logger::info(['request' => $this->data, 'api' => $url, 'response' => $user_info], 'third_taiping');
        if ($user_info['errCode'] && $user_info['errCode'] == 10002) {
            $i = 1;
            while ($user_info['errCode'] && $user_info['errCode'] == 10002 && $i < 7) {
                $i++;
                $user_info = Utilities::curl($url, $this->data, ['is_json' => 1, 'is_post' => 1]);
            }
            Logger::info(['request_retry' => $this->data, 'retry_num' => $i, 'api' => $url, 'response' => $user_info], 'third_taiping');
            if (!($user_info && $user_info['message'] == 'success')) {
                Utilities::DDMonitor("P3-pangu-获取用户信息失败后重试第{$i}次失败。" . json_encode($user_info, JSON_UNESCAPED_UNICODE), 'cloudm');
            }
        }
        if ($user_info['errCode']) {
            $user_info['error_code'] = $user_info['errCode'];
            $user_info['message'] = $user_info['errMsg'];
        }
        if (empty($user_info)) {
            Utilities::DDMonitor("P3-pangu-获取用户信息失败。", 'cloudm');
            $this->setView(10031, '用户信息获取失败，请联系工作人员。', '');
            return FALSE;
        }
        if ($user_info['error_code']) {
            Utilities::DDMonitor("P3-pangu-获取用户信息失败。返回：" . json_encode($user_info, JSON_UNESCAPED_UNICODE), 'cloudm');
            $this->setView(10032, '用户信息获取失败，请联系工作人员。', $user_info);
            return FALSE;
        }
        $t = time();
        $data = [
            'ts' => $t,
            'is_yingtong' => 1,
            'od_diopter' => (int) $user_info['data']['od_diopter'],
            'os_diopter' => (int) $user_info['data']['os_diopter'],
            'name' => $user_info['data']['name'],
            'sn' => md5($this->sn),
            'substr6Sn' => substr($this->sn, -6),
            'pcode' => $this->pcode,
            'work_mode' => 3,
        ];
        $patient_obj = new Patient();
        $extra_json = [
            'od_diopter' => $data['od_diopter'],
            'os_diopter' => $data['os_diopter'],
            'key' => $this->request->REQUEST['key'],
            'token' => $this->request->REQUEST['token'],
            'sn' => $this->sn,
        ];
        $patient_info = [
            'extra_json' => $extra_json,
            'uuid' => $this->pcode,
            'guid' => 'taiping_' . $this->request->REQUEST['token'],
            'status' => 1,
            'org_id' => $this->fd16_user['org_id'],
            'insurance_text' => ['customer_manager_id' => $user_info['data']['agent_num']],
        ];
        foreach (['name', 'gender', 'phone', 'birthday', 'height', 'weight'] as $field) {
            $patient_info[$field] = $user_info['data'][$field];
        }
        list($patient_id, $old_patient) = $patient_obj->handleIkangPatient($patient_info, 1);
        if (!$patient_id) {
            Utilities::DDMonitor("P3-pangu-handlePatient 更新用户信息失败。", 'cloudm');
            $patient_info['submit_user_name'] = $this->fd16_user['name'];
            $patient_info['submit_user_id'] = $this->fd16_user['user_id'];
            $this->setView(10033, '更新用户信息失败。', $patient_info);
            return FALSE;
        }
        $ret_patient_code = PatientCode::updatePatientIdInfo($this->pcode, $patient_id);
        $this->fd16_start_url = EYE_DOMAIN_HTTPS_PE . 'startScanner/startScanner?'
            . '&pcode=' . urlencode($this->pcode) . '&sn=' . $this->sn . '&ts=' . $t . "&work_mode=3&is_yingtong=1&name=" . urlencode($data['name'])
            . '&od_diopter=' . $data['od_diopter'] . '&os_diopter=' . $data['os_diopter'];
        RedisPcodeImgUrl::setCache($this->pcode . '_fd16_url', $this->fd16_start_url);
        $this->setView(0, 'success', $data);
        return TRUE;
    }

    private function _init()
    {
        $request = $this->request->REQUEST;
        $scene = trim($request['scene']);
        if ($scene) {
            $item = WechatScene::getItem($scene);
            if (!$item) {
                $this->setView(10041, '链接已失效，请重新扫描或联系工作人员获取帮助。', []);
                return FALSE;
            }
            foreach (['pcode', 'sn', 'key', 'token'] as $column) {
                $this->request->REQUEST[$column] = $request[$column] = trim($item['params'][$column]);
            }
        }
        $t = time();
        $this->data = ['sign_type' => 'sha1', 'timestamp' => $t];
        $i = 0;
        foreach (['pcode', 'sn', 'key', 'token'] as $column) {
            $i++;
            $this->data[$column] = trim($request[$column]);
            $column == 'sn' && $this->sn = trim($request[$column]);
            $column == 'pcode' && $this->pcode = trim($request[$column]);
            if (empty($this->data[$column])) {
                $this->setView(10010 + $i, '二维码失效，请联系工作人员获取正确的二维码。', []);
                return FALSE;
            }
        }
        $this->patient_code = PatientCode::getItemByPcode($this->data['pcode']);
        if ($this->patient_code['created'] < date('Y-m-d H:i:s', time() - CameraHandler::VALID_TIME_IN_SECONDS)) {
            $this->setView(10022, '链接已失效，请重新扫描或联系工作人员获取帮助。', []);
            return FALSE;
        }
        if ($this->patient_code['check_id']) {
            $this->setView(10023, '链接已使用，请重新扫描或联系工作人员获取帮助。', []);
            return FALSE;
        }
        $this->org_id = ThirdHandler::ORG_IDS['taiping'][0];
        if (strlen($this->sn) == 32) {
            $this->camera = CameraHandler::getCameraBySN($this->sn);
            $this->sn = $this->data['sn'] = $this->camera['sn'];
        } else {
            $this->camera = CameraHandler::getCameraOriginSN($this->sn)[0];
        }
        if (!$this->camera) {
            Utilities::DDMonitor("P3-pangu-【OP】请先绑定到【太平人寿】下的账号，SN为：{$this->sn}。", 'bigop', TRUE);
            $this->setView(10024, '二维码失效，请联系工作人员获取正确的二维码。', []);
            return FALSE;
        }
        $org_name = $this->fd16_user['org']['name'];
        $user_id = $this->camera['user_id'];
        $user_obj = new User();
        $this->fd16_user = $user_obj->getUserById($user_id);
        if (!in_array($this->fd16_user['org_id'], ThirdHandler::ORG_IDS['taiping'])) {
            Utilities::DDMonitor("P3-pangu-【OP】请先绑定到【太平人寿】下的账号，SN为：{$this->sn}。", 'bigop', TRUE);
            $this->setView(10024, '当前设备账号异常，请联系工作人员。', []);
            return FALSE;
        }
        if ($this->camera['work_mode'] == 4) {
            Utilities::DDMonitor("P3-pangu-【OP】【{$org_name}】机构下的设备请设置为指令模式，SN为：{$this->sn}。", 'bigop', TRUE);
            $this->setView(10025, '当前设备配置异常，请联系工作人员。', []);
            return FALSE;
        }
        if ($this->data['key'] != Xcrypt::encryptAes($this->data['sn'], ThirdHandler::AES_KEY['taiping'])) {
            Utilities::DDMonitor("P3-pangu-【OP】二维码异常，请跟进重新发放二维码，SN为：{$this->sn}。", 'bigop', TRUE);
            $this->setView(10021, '请联系工作人员获取正确的二维码。', []);
            return FALSE;
        }
        $org = Organizer::getOrgByIds($this->org_id)[$this->org_id];
        $this->data['signature'] = sha1($org['secret_key'] . $t . $this->data['token']);
        unset($this->data['sn'], $this->data['key'], $this->data['pcode']);
        return TRUE;
    }

}
