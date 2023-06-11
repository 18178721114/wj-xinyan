<?php
/**
 * Created by Hailong.
 */

namespace Air\Modules\Wechat;
use Air\Package\User\PatientCode;
use Air\Package\Patient\Patient;
use Air\Package\Checklist\CheckInfo;
use Air\Package\Wechat\Helper\RedisGeneralReport;
use Air\Package\Wechat\helper\RedisPcodeImgUrl;

class Patient_info_landing extends \Air\Libs\Controller
{
    public $must_login = false;
    public $general = 0;

    public function run()
    {
        $frm = trim($this->request->REQUEST['frm']);
        $result = PatientCode::getItemsByOpenid($this->token, 1, 1);
        if ($result) {
            $item = $result[0];
        }
        if ($frm == 'zx') {
            $img_url = RedisPcodeImgUrl::getCache($this->token . '_zx');
        }
        else {
            $img_url = RedisPcodeImgUrl::getCache($this->token);
        }
        if (!$img_url || $item['check_id']) {
            $img_url = '';
        }
        $check_id = RedisGeneralReport::getCache($this->token);
        $phone = RedisGeneralReport::getCache($this->token . '2phone');
		$path = 'report';
		if ($frm == 'zx') {
			$path = 'report';
		}
        $cobj = new CheckInfo();
        if ($check_id && $item['check_id'] == $check_id) {
            $url = EYE_DOMAIN_HTTPS_PE . 'user/' . $path . '/' . urlencode(\Air\Libs\Xcrypt::encrypt($check_id)) . '?frm=general';
            if ($frm == 'zx' && defined('EYE_DOMAIN_HTTPS_PE002')) {
                $url = EYE_DOMAIN_HTTPS_PE002 . 'user/' . $path . '/' . urlencode(\Air\Libs\Xcrypt::encrypt($check_id)) . '?frm=zx';
            }
            $check_info = $cobj->getCheckInfoSelfById($check_id);
            if (in_array($check_info[0]['org_id'], array_merge([FD16_ORG_ID, ICVD_ORG_ID, ICVD_ORG_ID_2, 41922], PA_HFL_ORG_ID))) {
                $url = '';
            }
            $this->setView(0, '', ['report_url' => $url, 'img_url' => $img_url]);
            return TRUE;
        }
        elseif ($phone) {
            $patient = Patient::getPatientByPhone($phone, 10, 0, 1);
            if ($patient) {
				$pids = [];
				if ($frm == 'zx') {
					foreach ($patient as $p) {
						if (substr($p['uuid'], 0, 4) == ZX_PCODE_PREFIX) {
							$pids[] = $p['patient_id'];
						}
					}
				}
				else {
					$pids = array_column($patient, 'patient_id');
				}
                $check_info = CheckInfo::getLatestCheckInfoByPatientId($pids);
                if ($check_info && !in_array($check_info[0]['org_id'], array_merge([FD16_ORG_ID, ICVD_ORG_ID, ICVD_ORG_ID_2, 41922], PA_HFL_ORG_ID))) {
                    $url = EYE_DOMAIN_HTTPS_PE . 'user/' . $path . '/' . urlencode(\Air\Libs\Xcrypt::encrypt($check_info[0]['check_id'])) . '?frm=general';
                    if ($frm == 'zx' && defined('EYE_DOMAIN_HTTPS_PE002')) {
                        $url = EYE_DOMAIN_HTTPS_PE002 . 'user/' . $path . '/' . urlencode(\Air\Libs\Xcrypt::encrypt($check_info[0]['check_id'])) . '?frm=zx';
                    }
                    $this->setView(0, '', ['report_url' => $url, 'img_url' => $img_url]);
                    return TRUE;
                }
            }
        }
        elseif ($item && !$item['check_id']) {
            $this->setView(0, '', ['img_url' => $img_url, 'report_url' => '']);
            return TRUE;
        }
        $this->setView(0, '', ['report_url' => '', 'img_url' => $img_url]);
        return false;
    }

}
