<?php

namespace Air\Modules\Wechat;

use Air\Libs\Base\Utilities;
use \Air\Package\Cache\RedisCache;
use Air\Package\Checklist\CheckLog;
use \Air\Package\Fd16\CameraHandler;
use \Air\Package\Patient\Patient;
use \Air\Package\User\PatientCode;
use \Air\Package\Rescan\RescanHandler;
use Air\Package\User\Product;

/**
 * 海外fd16英文版登记用户信息
 */
class Patient_info_add_oversea extends \Air\Libs\Controller
{
    public $must_login = TRUE;
    private $params = [];

    public function run()
    {
        if (!$this->_init()) {
            return FALSE;
        }
        
        $request = $this->request->REQUEST;

        $pcode_item = PatientCode::getItemByPcode($this->params['uuid']);
        if (empty($pcode_item)) {
            $this->setView(10001, '筛查码无效，请重新扫码获取。', []);
            return FALSE;
        }
        $patient = new Patient();
        $patient_id = $patient->addPatient($this->params);
        PatientCode::updatePatientIdInfo($this->params['uuid'], $patient_id);

        //如果有sn，启动设备处理
        if ($this->params['sn']) {
            $camera_info = CameraHandler::getCameraOriginSN($this->params['sn']);
            if(!$camera_info || $camera_info[0]['user_id'] != $this->userSession['user_id']) {
                $this->setView(10013, '您尚未绑定设备，请先绑定。');//您尚未绑定设备，请先绑定。
                return FALSE;
            }

            $lock = RescanHandler::handleLock($this->params['uuid'], 'scan');
            if ($lock === -1) {
                $this->setView(10011, '您已生成报告，无法重新扫描');//您已生成报告，无法重新扫描
                return FALSE;
            } elseif (!$lock) {
                $this->setView(10012, '您已经开始重新扫描，请不要重复该请求。');//您已经开始重新扫描，请不要重复该请求。
                return FALSE;
            }

            if (strlen($this->params['sn']) != 32) {
                $this->params['sn'] = md5($this->params['sn']);
            }
            $ts = time();
            $os_diopter = $this->params['os_diopter'] ?? '';
            $od_diopter = $this->params['os_diopter'] ?? '';
            $compel_remake = '';

            $language_type = isset($request['language_type']) ? intval($request['language_type']) : 0; //设备语言类型

            //$camera_status = 5;//启动相机失败
            $camera_status = CameraHandler::startCamera($this->params['uuid'], $this->params['sn'], $ts, $os_diopter, $od_diopter, $compel_remake, 1, $language_type);

            if ($camera_status > 0) {
                RescanHandler::unLock($this->params['uuid'], 'scan');
            }
            if($camera_status == 0) {
                CheckLog::addLogInfo(0, 'patient_info_add_oversea_start_camera_success', ['data' => ['params' => $this->params]], 0, '', $this->params['uuid']);
                $this->setView(0, '启动相机成功', $this->params['uuid']);//启动相机成功
            } else {
                CheckLog::addLogInfo(0, 'patient_info_add_oversea_start_camera_failed', ['data' => ['params' => $this->params, 'camera_status' => $camera_status]], 0, '', $this->params['uuid']);
                $this->setView('101'.str_pad($camera_status, 2, '0', STR_PAD_LEFT), '启动相机失败');//启动相机失败
            }
        } else {
            CheckLog::addLogInfo(0, 'patient_info_add_oversea_success', ['data' => ['params' => $this->params]], 0, '', $this->params['uuid']);
            $this->setView(0, '提交成功', $this->params['uuid']);//提交成功
        }

        return FALSE;
    }

    private function _init()
    {
        $request = $this->request->REQUEST;
        foreach ($request as &$val) {
            if (is_string($val)) {
                $val = trim($val);
            }
        }
        if (empty($request['email']) && empty($request['phone'])) {
            $this->setView(10003, "手机号或email是必须的", []);//手机号或email是必须的
            return FALSE;
        }
        $this->params['org_id'] = $this->userSession['org_id'];
        $this->params['phone'] = empty($request['phone']) ? '18601234567' : $request['phone'];
        $this->params['email'] = $request['email'];
        $this->params['id_number_crypt'] = !empty($request['id_number_crypt']) ? $request['id_number_crypt'] : '';
        $this->params['status'] = 1;

        if(isset($request['sn']) && $request['sn']) {
            $this->params['sn'] = trim($request['sn']);
        } else {
            $this->params['sn'] = '';
        }

        $required = ['name', 'gender', 'age'];
        $optional = ['medical_history', 'complained', 'height', 'weight', 'od_diopter', 'os_diopter'];
        foreach ($required as $column) {
            if (empty($request[$column])) {
                $this->setView(10003, "column {$column} is required!", []);//。。。是必须的
                return FALSE;
            }
            $this->params[$column] = $request[$column];
        }
        $this->params['age'] = (int) $this->params['age'];
        if ($this->params['age'] <= 3 || $this->params['age'] > 120) {
            $this->setView(10004, "年龄不符合规范", []);
            return FALSE;
        } 
        if (!in_array($this->params['gender'], [1, 2])) {
            $this->setView(10004, "性别不符合规范", []);
            return FALSE;
        } 
        if (strlen($this->params['name']) > 50) {
            $this->setView(10005, "姓名过长，请不要超过50个字符", []);//姓名过长，请不要超过50个字符
            return FALSE;
        }
        if (strlen($this->params['email']) > 100 && !strpos($this->params['email'], '@')) {
            $this->setView(10005, "邮箱不符合规范", []);
            return FALSE;
        }
        foreach ($optional as $column) {
            if (in_array($column, ['height', 'weight'])) {
                $this->params[$column] = $request[$column];
            }
            else {
                $this->params['extra_json'][$column] = $request[$column];
                if (in_array($column, ['medical_history'])) {
                    $this->params['extra'][$column] = $request[$column];
                }
            }
        }
        $this->params['extra_json']['email'] = $this->params['email'];
        // unset($this->params['email']);

        $language = isset($request['language']) ? trim($request['language']) : '';
        if ($language && $language != 'zh_CN') {
            $language = Utilities::getLocale($language);
        }
        if ($language) {
            $this->params['extra_json']['language'] = $language;
        }

        $package_id = isset($request['package_id']) ? intval($request['package_id']) : 0;
        if (!$this->checkPackage($package_id, $this->userSession['org_id'])) {
            $this->setView(10020, '请选择使用的产品套餐！', []);
            return FALSE;
        }
        if ($package_id) {
            $this->params['extra_json']['package_id'] = $package_id;
        }
        
        $openid = $this->token;
        //海外使用糖网套餐(8990)
        list($id, $this->params['uuid']) = PatientCode::initCode($openid, '8990', 1, 0, -1, $this->userSession['user_id'], $this->userSession['org_id']);
        if (isset($request['height']) && !$this->checkHeight($request['height'])) {
            return FALSE;
        }
        if (isset($request['weight']) && !$this->checkWeight($request['height'])) {
            return FALSE;
        }
        
        $this->params['birthday'] = date('Y-m-d', time() - $this->params['age'] * 31622400);
        unset($this->params['age']);
    
        if ($this->params['uuid'] && isset($this->params['od_diopter']) && isset($this->params['os_diopter'])) {
            $diopter = [
                'OD' => $this->params['od_diopter'],
                'OS' => $this->params['os_diopter'],
            ];
            RedisCache::setCache($this->params['uuid'], serialize($diopter), CameraHandler::PCODE_PREFIX, 86400 * 30);
        }
        return true;
    }

    private function checkWeight($weight)
    {
        if ($weight < 10 || $weight > 300) {
            $this->setView(10003, '体重不符合规范', []);
            return FALSE;
        }

        return true;
    }

    private function checkHeight($height)
    {
        if ($height < 80 || $height > 250) {
            $this->setView(10003, '身高不符合规范', []);
            return FALSE;
        }

        return true;
    }

    //在机构有套餐的时候，套餐为必选项
    private function checkPackage($package_id, $org_id)
    {
        $package_res = Product::getProductByOrgId($org_id);
        $package_ids = count($package_res) > 0 ? array_column($package_res, 'package_id') : [];

        if (!$package_ids && !$package_id) {
            return TRUE;
        }
        if ($package_ids && in_array($package_id, $package_ids)) {
            return TRUE;
        }

        return FALSE;
    }
}
