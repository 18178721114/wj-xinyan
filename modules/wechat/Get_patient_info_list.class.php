<?php
/**
 * Created by Hailong.
 */

namespace Air\Modules\Wechat;

use Air\Libs\Base\Utilities;
use Air\Libs\Xcrypt;
use Air\Package\Checklist\CheckInfoUtil;
use Air\Package\Fd16\CameraHandler;
use Air\Package\User\PatientCode;
use Air\Package\Patient\Patient;
use Air\Package\User\User;

class Get_patient_info_list extends \Air\Libs\Controller
{
    public $must_login = false;
    public $general = 0;

    public function run()
    {
        $en_open_id = trim($this->request->REQUEST['en_open_id']);
        $phone = trim($this->request->REQUEST['phone']);
        if (!$en_open_id || $en_open_id == "undefined" || $en_open_id == "null") {
            $this->setView(90003, 0, '');
            return false;
        }
        $openid = Xcrypt::decrypt($en_open_id);
        $sn = trim($this->request->REQUEST['sn']);
        $org_ids = [];
        $is_pingan = 0;
        if ($sn) {
            $camera = CameraHandler::getCameraBySN($sn);
            $user_id = $camera['user_id'];
            $u_obj = new User();
            $user = $u_obj->getUserById($user_id);
            if (in_array($user['org']['config']['business_line'], [4, 5])) {
                $this->setView(90002, 0, '');
                return false;
            }
            if (in_array($user['org_id'], PA_ALL_ID)) {
                $org_ids = PA_ALL_ID;
            }
            if ($user['org_id'] == PA_ZY_ORG_ID && PA_SWITCH == 1 || $user['org_id'] == PA_APP_ORG_ID && PA_APP_SWITCH == 1) {
                $is_pingan = 1;
            }
        } else {
            $pa_phone = Xcrypt::aes_decrypt($openid);
            if ($pa_phone && Utilities::isPhone($pa_phone)) {
                $phone_patients = Patient::getPatientByPhone($pa_phone, 50, PA_ALL_ID, 2);
            }
        }
        $result = PatientCode::getItemsByOpenid($openid);
        if ($result || $phone_patients) {
            if ($result && count($result) > 100) {
                $result = array_slice($result, 0, 100);
            }
            $pcode = $phone_uuid = [];
            if ($result) {
                $pcode = array_column($result, 'pcode');
            }
            if ($phone_patients) {
                $phone_uuid = array_column($phone_patients, 'uuid');
            }
            $pcode = array_values(array_unique(array_merge($pcode, $phone_uuid)));
            $patients = Patient::getPatientsByUuids($pcode, 'uuid', 1, ['patient_id' => 1], ['limit' => ['page_num' => 50, 'offset' => 0]], $org_ids, [$phone]);
            if ($patients) {
                $exist = [];
                foreach ($patients as $uuid => &$item) {
                    if (isset($exist[$item['name']]) || !$patients[$uuid]['status']) {
                        unset($patients[$uuid]);
                        continue;
                    }
                    unset($item['insurance_text']);
                    $item['created'] = date('Y.m.d', strtotime($item['created']));
                    $exist[$item['name']] = 1;
                }
                if (count($patients) > 20) {
                    $patients = array_slice($patients, 0, 20);
                }
                $patients = array_values($patients);
                $patients = Utilities::sortArray($patients, 'patient_id', 'DESC');
                $count = count($patients);
                $patients[0]['is_pingan'] = $is_pingan;
                if ($is_pingan) {
                    $effective_check_infos = CheckInfoUtil::getEffectiveCheckInfoByOpenid($openid, PA_ALL_ID);
                    if ($effective_check_infos) {
                        $patients[0]['has_effective_check'] = 1;
                    }
                    if ($user['config']['switch_yh_pay'] == 1 || $user['config']['switch_yh_pay'] == -1 && $user['org']['config']['switch_yh_pay'] == 1) {
                        $patients[0]['switch_yh_pay'] = 1;
                    }
                }
                if ($user['config']['child_report'] == 1 || $user['config']['child_report'] == -1 && $user['org']['config']['child_report'] == 1) {
                    $patients[0]['child_report'] = 1;
                }
                $this->setView(0, $count, $patients);
                return TRUE;
            }
        }
        if ($is_pingan) {
            $this->setView(1, 0, '');
            return false;
        } else {
            $this->setView(90001, 0, '');
            return false;
        }
    }
}
