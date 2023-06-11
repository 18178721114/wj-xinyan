<?php
namespace Air\Modules\Wechat;

use Air\Libs\Xcrypt;
use Air\Package\Patient\Patient;
use Air\Package\Checklist\CheckInfo;
use Air\Package\Checklist\CheckInfoExtra;
use Air\Package\Checklist\CheckLog;
use Air\Package\Sari\SariQa;
use Air\Package\Fd16\WayZHandler;
use Air\Package\User\PatientCode;

class Patient_info_add_qr extends \Air\Libs\Controller
{
    public $must_login = false;

    const NAMES = [
        '北京市北京城区',
        '天津市天津城区',
        '上海市上海城区',
        '重庆市市辖区',
        ' ',
    ];

    const REPLACE_NAMES = [
        '北京市',
        '天津市',
        '上海市',
        '重庆市',
        '',
    ];

    public function run()
    {
        if (!$this->_init()) {
            return false;
        }
        $request = $this->request;
        $name = $this->name;
        $gender = $this->gender;
        $birthday = $this->birthday;
        $height = trim($request->REQUEST['height']);
        $weight = trim($request->REQUEST['weight']);
        $phone = $this->phone ? $this->phone : '';
        $address = $this->address ? $this->address : '';
        $so2 = trim($request->REQUEST['SO2']);
        $t = trim($request->REQUEST['T']);
        $r = trim($request->REQUEST['R']);
        $p = trim($request->REQUEST['P']);
        $home_address = trim($request->REQUEST['home_address']) ? trim($request->REQUEST['home_address']) : '';
        $office_address = trim($request->REQUEST['office_address']) ? trim($request->REQUEST['office_address']) : '';
        $addresses = trim($request->REQUEST['addresses']) ? trim($request->REQUEST['addresses']) : '';
        $data = [];
        $data['complained'] = trim($request->REQUEST['complained']);
        $other_complained = trim($request->REQUEST['other_complained']);
        if ($other_complained) {
            $data['complained'] = $data['complained']
                ? $data['complained'].";".$other_complained
                : $other_complained;
        }
        $data['medical_history'] = trim($request->REQUEST['medical_history']);
        $data['other_history'] = trim($request->REQUEST['other_history']);
        $pobj = new Patient();
        if ($this->id_number) {
            $gender = substr($this->id_number, 16, 1) % 2 == 1 ? 1 : 2;
            $birthday = substr($this->id_number, 6, 8);
        }
        $params = [
            "birthday" => $birthday,
            "gender" => $gender,
            "height" => $height,
            "weight" => $weight,
            "id_number_crypt" => $this->id_number,
            "id_type" => $this->id_type,
            "name" => $name,
            "status" => 1,
            'phone' => $phone,
            'address' => $address,
            'medical_record_no' => $this->job_number,
        ];
        $extra_json = [];
        if ($home_address) {
            $home_address = str_replace(self::NAMES, self::REPLACE_NAMES, $home_address);
            $extra_json['home_address'] = $home_address;
            \Phplib\Tools\Logger::info('home_address='.$home_address, 'wayz');
            $home_sari_risk = WayZHandler::getSariRiskByAddress($home_address);
            if ($home_sari_risk) {
                $home_sari_risk_str = json_encode($home_sari_risk, JSON_UNESCAPED_UNICODE);
                $data['extra']['home_sari_risk'] = $home_sari_risk_str;
                $extra_json['home_sari_risk'] = $home_sari_risk_str;
            }
            if ($office_address) {
                $office_address = str_replace(self::NAMES, self::REPLACE_NAMES, $office_address);
                $extra_json['office_address'] = $office_address;
                \Phplib\Tools\Logger::info('office_address='.$office_address, 'wayz');
                $office_sari_risk = WayZHandler::getSariRiskByAddress($office_address);
                if ($office_sari_risk) {
                    $office_sari_risk_str = json_encode($office_sari_risk, JSON_UNESCAPED_UNICODE);
                    $data['extra']['office_sari_risk'] = $office_sari_risk_str;
                    $extra_json['office_sari_risk'] = $office_sari_risk_str;
                }
            }
        } else if ($addresses) {
            $addresses_list = explode(',', $addresses);
            $address_result = [];
            $from_risk_address = false;
            foreach ($addresses_list as $address) {
                if (!$from_risk_address) {
                    $from_risk_address = $this->isAddressRisk($address);
                }
                $address = str_replace(self::NAMES, self::REPLACE_NAMES, $address);
                $address_risk = WayZHandler::getSariRiskByAddress($address);
                $item = ['address' =>  $address];
                if ($address_risk) {
                    $item['risk'] = $address_risk;
                }
                $address_result[] = $item;
            }
            $extra_json['address_risk'] = json_encode($address_result, JSON_UNESCAPED_UNICODE);
        }

        if ($this->sari_answer) {
            foreach ($this->sari_answer as $key => $value) {
                $data['extra']['sari_qa_' . $key] = $value;
                $extra_json['sari_qa_' . $key] = $value;
            }
            $sari_qa_risk_level = SariQa::getSariRiskLevel($this->sari_answer);
            $data['extra']['sari_qa_risk_level'] = $sari_qa_risk_level;
            $extra_json['sari_qa_risk_level'] = $sari_qa_risk_level;
        } else {
            if (isset($request->REQUEST['new_sari_qa1'])) {
                $sari_qa = [];
                $extra_json['new_sari_qa1'] = $request->REQUEST['new_sari_qa1'];
                $sari_qa[] = $request->REQUEST['new_sari_qa1'];
                $extra_json['new_sari_qa2'] = $request->REQUEST['new_sari_qa2'];
                $sari_qa[] = $request->REQUEST['new_sari_qa2'];
                $extra_json['new_sari_qa3'] = $request->REQUEST['new_sari_qa3'];
                $sari_qa[] = $request->REQUEST['new_sari_qa3'];
                $extra_json['new_sari_qa4'] = $request->REQUEST['new_sari_qa4'];
                $sari_qa[] = $request->REQUEST['new_sari_qa4'];
                if (isset($request->REQUEST['new_sari_qa5']) && $request->REQUEST['new_sari_qa5'] !== '' ) {
                    $extra_json['new_sari_qa5'] = $request->REQUEST['new_sari_qa5'];
                    $sari_qa[] = $request->REQUEST['new_sari_qa5'];
                }
                $sari_qa_risk_level = SariQa::getNewSariRiskLevel($sari_qa, $t, $from_risk_address);
                $extra_json['sari_qa_risk_level'] = $sari_qa_risk_level;
            }
        }

        if ($t && $p && $r && $so2) {
            $check_item = [
                't' => $t,
                'p' => $p,
                'r' => $r,
                'so2' => $so2,
            ];
            $check_item_str = json_encode($check_item);
            $data['extra']['check_item'] = $check_item_str;
            $extra_json['check_item'] = $check_item_str;
        }

        if ($extra_json) {
            $params['extra_json'] = $extra_json;
        }

        $pobj = new Patient();
        if ($this->pcode) {
            $this->pcode = \Air\Libs\Xcrypt::decrypt(rawurldecode(str_replace(' ', '+', $this->pcode)));
            if (!$this->pcode) {
                $this->setView(10002, '参数错误', []);
                return false;
            }
            $patient = $pobj->getPatientByUuid($this->pcode);
            if (empty($patient)) {
                $params['uuid'] = $this->pcode;
                list($pid, $dummy) = $pobj->handleIkangPatient($params, 1);
            } else {
                $params['patient_id'] = $patient['patient_id'];
                $pobj->updatePatient($params);
                $pid = $patient['patient_id'];
            }
            $old_check = CheckInfo::getOneFinishedCheckInfoByPatientId($pid);
            PatientCode::updatePatientIdInfo($this->pcode, $pid);
            if ($old_check && $old_check[0]['check_id']) {
                $cobj = new CheckInfo();
                $cobj->clearCache($old_check[0]['check_id']);
            }
            if ($old_check[0]['check_id']) {
                CheckLog::addLogInfo($old_check[0]['check_id'], 'patient_info_add_qr', ['data' => ['params' => $params]]);
            } else {
                CheckLog::addLogInfo(0, 'patient_info_add_qr', ['data' => ['params' => $params]], 0, '', $this->pcode);
            }
            $this->setView(0, '信息添加成功', [Xcrypt::encrypt($pid)]);
        } else if ($this->check_id) {
            $cobj = new CheckInfo();
            $check_infos = $cobj->getCheckInfoSelfById($this->check_id);
            if (!$check_infos) {
                $this->setView(10011, '检查单不存在！', []);
                return;
            }
            $pid = $check_infos[0]['patient_id'];
            $params['patient_id'] = $pid;
            $pobj->updatePatient($params);
            $check_info = $check_infos[0];
            $check_id = $check_info['check_id'];
            $data['check_id'] = $check_id;
            CheckInfoExtra::updateInfo($data);
            $cobj->clearCache($this->check_id);
            $this->setView(0, '报告已生成', [Xcrypt::encrypt($check_id)]);
        }

        return true;
    }

    private function _init()
    {
        $request = $this->request;
        $this->name = trim($request->REQUEST['name']);
        if (empty($this->name)) {
            $this->setView(10003, '缺少参数', []);
            return false;
        }
        if (isset($request->REQUEST['sari_qa'])) {
            $sari_qa = $request->REQUEST['sari_qa'];
            $this->sari_answer = SariQa::handleSariAnswer($sari_qa) ;
            if (!$this->sari_answer) {
                $this->setView(10003, '参数错误：sari_qa', []);
                return false;
            }
        }

        if (empty($request->REQUEST['en_check_id']) && empty($request->REQUEST['pcode'])) {
            $this->setView(10003, '缺少参数', []);
            return false;
        }

        $this->check_id = Xcrypt::decrypt($request->REQUEST['en_check_id']);
        $this->pcode = trim($request->REQUEST['pcode']);
        $this->id_number = $request->REQUEST['id_num'];
        if (empty($this->id_number)) {
            $this->setView(10003, '身份证号必填', []);
            return false;
        }

        $this->phone = $request->REQUEST['phone'];
        $this->address = $request->REQUEST['address'];
        $this->id_type = 2;

        return true;
    }

    private function isAddressRisk($address)
    {
        if (mb_substr($address, 0, 2)  == '湖北') {
            return true;
        }

        return false;
    }
}
