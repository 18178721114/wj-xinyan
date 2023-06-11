<?php
namespace Air\Modules\Wechat;

use Air\Libs\ParamValidate;
use Air\Package\Checklist\CheckInfo;
use Air\Package\Patient\Patient;
use Air\Package\Checklist\CheckInfoExtra;
use Air\Package\Checklist\CheckLog;
use Air\Package\User\Organizer;
use Air\Package\User\PatientCode;

/**
 * BIZAA-71 一体机项目互联网医院可以修改主诉
 */
class Patient_update_h5 extends \Air\Libs\Controller
{
    // public $must_login = true;
    use ParamValidate;
    public function run()
    {
        if (!$this->_init()) {
            return FALSE;
        }
        $pobj = new Patient();
        $request = $this->request;
        $pcode_item = PatientCode::getItemByPcode($this->pcode);
        if (!$pcode_item) {
            $this->setView(10002, 'pcode不存在', []);
            return FALSE;
        }
        if ($pcode_item['created'] < date('Y-m-d H:i:s', time() - 864000)) {
            $this->setView(10003, 'pcode已过期', []);
            return FALSE;
        }
        if (!$pcode_item['org_id']) {
            $this->setView(10004, '机构ID不能为空', []);
            return FALSE;
        }
        $org = Organizer::getOrganizerByIds($pcode_item['org_id']);
        if (is_string($org[$pcode_item['org_id']]['config'])) {
            $org[$pcode_item['org_id']]['config'] = json_decode($org[$pcode_item['org_id']]['config'], 1);
        }
        if ($org[$pcode_item['org_id']]['config']['business_line'] != 20) {
            $this->setView(10004, '机构不符合规范', []);
            return FALSE;
        }
        $this->patient_id = $pcode_item['patient_id'];
        $complained = trim($request->REQUEST['complained']);
        $data = [];
        if ($pcode_item['check_id'] && $complained) {
            $data['check_id'] = $pcode_item['check_id'];
            $old = CheckInfoExtra::getCheckInfoExtraSelfByIds($this->check_id)[$this->check_id];
            $old_data = [
              'complained' => $old['complained'],
            ];
            $data['complained'] = $complained;
            $ret1 = CheckInfoExtra::updateInfo($data);
        }

        $params = [
            'patient_id' => $pcode_item['patient_id'],
        ];
        $extra_json = ['doctor_consulting_applied' => 1];
        if ($complained) {
            $extra_json['complained'] = $complained;
        }
        $params['extra_json'] = $extra_json;
        $old_patient = $pobj->getPatientsByIds($this->patient_id)[$this->patient_id];
        $ret2 = $pobj->updatePatient($params, $pcode_item['check_id']);
        $cobj = new CheckInfo();
        $cobj->clearCache($pcode_item['check_id']);
        $old_params = [
            'extra_json' => $old_patient['extra_json'],
        ];
        if ($ret2 || $ret1) {
            $check_log_remark = ['change' => [
                "[" . json_encode($old_data) . " 变更为 " . json_encode($data) . "]",
                "[" . json_encode($old_params) . " 变更为 " . json_encode($params) . "]",
            ]];
            CheckLog::addLogInfo($this->check_id, 'patient_info_update', ['data' => $check_log_remark]);
            $this->setView(0, '修改信息成功', []);
            return true;
        }
        $this->setView(0, '', []);
        return false;
    }

    public $param_crypted = ['pcode'];
    public $param_required = [
        'pcode' => 'pcode',
    ];
    private function _init()
    {
        $request = $this->request;
        // 参数基础验证
        $verified = $this->_init_param();
        if (!$verified) {
            return FALSE;
        }
        return true;
    }
}
