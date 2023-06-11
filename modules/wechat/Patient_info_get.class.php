<?php
namespace Air\Modules\Wechat;

use Air\Libs\ParamValidate;
use Air\Package\Checklist\CheckInfo;
use Air\Package\Patient\Patient;
use Air\Package\User\Organizer;
use Air\Package\User\PatientCode;

/**
 * BIZAA-71 一体机项目互联网医院，展示预登记的信息
 */
class Patient_info_get extends \Air\Libs\Controller
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
        $cobj = new CheckInfo();

        if (!$pcode_item) {
            $this->setView(10002, gettext('pcode不存在'), []);
            return FALSE;
        }
        if (!$pcode_item['org_id']) {
            $this->setView(10004, gettext('机构ID不能为空'), []);
            return FALSE;
        }
        $org = Organizer::getOrganizerByIds($pcode_item['org_id']);

        if (is_string($org[$pcode_item['org_id']]['config'])) {
            $org[$pcode_item['org_id']]['config'] = json_decode($org[$pcode_item['org_id']]['config'], 1);
        }
        if ($org[$pcode_item['org_id']]['config']['business_line'] != 20) {
            $this->setView(10004, gettext('机构不符合规范'), []);
            return FALSE;
        }
        $this->patient_id = $pcode_item['patient_id'];
        $patient = $pobj->getPatientById($this->patient_id, 0, 1);

        $patient['medical_history_str'] = $cobj->renderHistoryClean($patient['extra']['medical_history']);
        unset($patient['phone_crypt'], $patient['extra_json'], $patient['extra']);
        $this->setView(0, '', $patient);
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
