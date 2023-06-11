<?php

namespace Air\Modules\Wechat;

use Air\Libs\Xcrypt;
use Air\Package\Checklist\CheckInfo;
use Air\Package\Checklist\CheckInfoExtra;
use Air\Package\Checklist\IcvdReport;
use Air\Package\User\Organizer;
use Air\Package\Patient\Patient;
use Air\Package\User\PatientCode;
use Air\Package\User\User;

class Voice_risk_level extends \Air\Libs\Controller
{
    public $must_login = FALSE;
    //arteriosclerosis 动脉弹性改变风险
    //endocrine_disorder 内分泌失调
    //myocardial 心肌健康风险
    //cerebral 脑缺血风险
    //anemia 血气不足风险
    //brain_tumor 脑肿瘤风险
    const MAN_AUDIO = [
        'arteriosclerosis' => [
            0 => ['m' => '', 's' => 18, 'audio' => 'https://img3.airdoc.com/audio/man/arteriosclerosis_0.mp3'],
            1 => ['m' => '', 's' => 17, 'audio' => 'https://img3.airdoc.com/audio/man/arteriosclerosis_1.mp3'],
            2 => ['m' => '', 's' => 20, 'audio' => 'https://img3.airdoc.com/audio/man/arteriosclerosis_2.mp3'],
            3 => ['m' => '', 's' => 14, 'audio' => 'https://img3.airdoc.com/audio/man/arteriosclerosis_3.mp3']
        ],
        'endocrine_disorder' => [
            0 => ['m' => '', 's' => 11, 'audio' => 'https://img3.airdoc.com/audio/man/endocrine_disorder_0.mp3'],
            1 => ['m' => '', 's' => 15, 'audio' => 'https://img3.airdoc.com/audio/man/endocrine_disorder_1.mp3'],
            2 => ['m' => '', 's' => 17, 'audio' => 'https://img3.airdoc.com/audio/man/endocrine_disorder_2.mp3'],
            3 => ['m' => '', 's' => 14, 'audio' => 'https://img3.airdoc.com/audio/man/endocrine_disorder_3.mp3']
        ],
        'myocardial' => [
            0 => ['m' => '', 's' => 14, 'audio' => 'https://img3.airdoc.com/audio/man/myocardial_0.mp3'],
            1 => ['m' => '', 's' => 16, 'audio' => 'https://img3.airdoc.com/audio/man/myocardial_1.mp3'],
            2 => ['m' => '', 's' => 25, 'audio' => 'https://img3.airdoc.com/audio/man/myocardial_2.mp3'],
            3 => ['m' => '', 's' => 11, 'audio' => 'https://img3.airdoc.com/audio/man/myocardial_3.mp3']
        ],
        'cerebral' => [
            0 => ['m' => '', 's' => 12, 'audio' => 'https://img3.airdoc.com/audio/man/cerebral_0.mp3'],
            1 => ['m' => '', 's' => 13, 'audio' => 'https://img3.airdoc.com/audio/man/cerebral_1.mp3'],
            2 => ['m' => '', 's' => 21, 'audio' => 'https://img3.airdoc.com/audio/man/cerebral_2.mp3'],
            3 => ['m' => '', 's' => 10, 'audio' => 'https://img3.airdoc.com/audio/man/cerebral_3.mp3']
        ],
        'anemia' => [
            0 => ['m' => '', 's' => 10, 'audio' => 'https://img3.airdoc.com/audio/man/anemia_0.mp3'],
            1 => ['m' => '', 's' => 12, 'audio' => 'https://img3.airdoc.com/audio/man/anemia_1.mp3'],
            2 => ['m' => '', 's' => 16, 'audio' => 'https://img3.airdoc.com/audio/man/anemia_2.mp3'],
            // 3 => ['m' => '','s' => '','audio' => 'anemia_3.mp3']
        ],
        'brain_tumor' => [
            0 => ['m' => '', 's' => 13, 'audio' => 'https://img3.airdoc.com/audio/man/brain_tumor_0.mp3'],
            1 => ['m' => '', 's' => 16, 'audio' => 'https://img3.airdoc.com/audio/man/brain_tumor_1.mp3'],
            2 => ['m' => '', 's' => 16, 'audio' => 'https://img3.airdoc.com/audio/man/brain_tumor_2.mp3'],
            //3 => ['m' => '','s' => '','audio' => 'brain_tumor_3.mp3']
        ],


    ];
    const WOMAN_AUDIO = [
        'arteriosclerosis' => [
            0 => ['m' => '', 's' => 21, 'audio' => 'https://img3.airdoc.com/audio/woman/arteriosclerosis_0.mp3'],
            1 => ['m' => '', 's' => 23, 'audio' => 'https://img3.airdoc.com/audio/woman/arteriosclerosis_1.mp3'],
            2 => ['m' => '', 's' => 28, 'audio' => 'https://img3.airdoc.com/audio/woman/arteriosclerosis_2.mp3'],
            3 => ['m' => '', 's' => 16, 'audio' => 'https://img3.airdoc.com/audio/woman/arteriosclerosis_3.mp3']
        ],
        'endocrine_disorder' => [
            0 => ['m' => '', 's' => 13, 'audio' => 'https://img3.airdoc.com/audio/woman/endocrine_disorder_0.mp3'],
            1 => ['m' => '', 's' => 19, 'audio' => 'https://img3.airdoc.com/audio/woman/endocrine_disorder_1.mp3'],
            2 => ['m' => '', 's' => 23, 'audio' => 'https://img3.airdoc.com/audio/woman/endocrine_disorder_2.mp3'],
            3 => ['m' => '', 's' => 18, 'audio' => 'https://img3.airdoc.com/audio/woman/endocrine_disorder_3.mp3']
        ],
        'myocardial' => [
            0 => ['m' => '', 's' => 19, 'audio' => 'https://img3.airdoc.com/audio/woman/myocardial_0.mp3'],
            1 => ['m' => '', 's' => 22, 'audio' => 'https://img3.airdoc.com/audio/woman/myocardial_1.mp3'],
            2 => ['m' => '', 's' => 37, 'audio' => 'https://img3.airdoc.com/audio/woman/myocardial_2.mp3'],
            3 => ['m' => '', 's' => 15, 'audio' => 'https://img3.airdoc.com/audio/woman/myocardial_3.mp3']
        ],
        'cerebral' => [
            0 => ['m' => '', 's' => 17, 'audio' => 'https://img3.airdoc.com/audio/woman/cerebral_0.mp3'],
            1 => ['m' => '', 's' => 18, 'audio' => 'https://img3.airdoc.com/audio/woman/cerebral_1.mp3'],
            2 => ['m' => '', 's' => 29, 'audio' => 'https://img3.airdoc.com/audio/woman/cerebral_2.mp3'],
            3 => ['m' => '', 's' => 13, 'audio' => 'https://img3.airdoc.com/audio/woman/cerebral_3.mp3']
        ],
        'anemia' => [
            0 => ['m' => '', 's' => 13, 'audio' => 'https://img3.airdoc.com/audio/woman/anemia_0.mp3'],
            1 => ['m' => '', 's' => 18, 'audio' => 'https://img3.airdoc.com/audio/woman/anemia_1.mp3'],
            2 => ['m' => '', 's' => 22, 'audio' => 'https://img3.airdoc.com/audio/woman/anemia_2.mp3'],
            //3 => ['m' => '','s' => '','audio' => 'anemia_3.mp3']
        ],
        'brain_tumor' => [
            0 => ['m' => '', 's' => 17, 'audio' => 'https://img3.airdoc.com/audio/woman/brain_tumor_0.mp3'],
            1 => ['m' => '', 's' => 22, 'audio' => 'https://img3.airdoc.com/audio/woman/brain_tumor_1.mp3'],
            2 => ['m' => '', 's' => 21, 'audio' => 'https://img3.airdoc.com/audio/woman/brain_tumor_2.mp3'],
            //3 => ['m' => '','s' => '','audio' => 'brain_tumor_3.mp3']
        ],


    ];

    public function run()
    {
        $request = $this->request->REQUEST;
        if (!$this->_init()) {
            return false;
        }
        //echo \Air\Libs\Xcrypt::encrypt(2592904);die;
        $check_id = \Air\Libs\Xcrypt::decrypt(rawurldecode(str_replace(' ', '+', trim($request['en_check_id']))));
        $check_info =  CheckInfo::getCheckByCheckId($check_id);
        //$user_obj = new User();
        //$fd16_user = $user_obj->getUserById($check_info['submit_user_id']);
        // $show_intelligence_audio = 0;
        // if (!isset($fd16_user['config']['show_intelligence_audio']) || $fd16_user['config']['show_intelligence_audio'] == -1) {
        //     $show_intelligence_audio = intval($fd16_user['org']['config']['show_intelligence_audio']);
        // } else {
        //     $show_intelligence_audio = intval($fd16_user['config']['show_intelligence_audio']);
        // }
        // if (!$show_intelligence_audio) {
        //     $this->setView($this->error_code_prefix . '03', '该机构没有开通智能语音报告解读', '');
        //     return FALSE;
        // }
        if (!$check_info) {
            $this->setView($this->error_code_prefix . '04', '暂无数据', '');
            return FALSE;
        }
        $icvd_obj = new IcvdReport();
        $org_obj = new Organizer();
        $patient_data = Patient::getPatientSelfById($check_info['patient_id']);
        $result = [];
        $result['userName'] = $patient_data['name'];
        $result['gender'] = $patient_data['gender'];
        $org_info = $org_obj->getOrganizerById($check_info['org_id']);
        $icvd_risk_items = $org_info['config']['icvd_risk_items'] ? explode(',', $org_info['config']['icvd_risk_items']) : [];
        list($unset_keys, $unset_group_keys) = $icvd_obj->getUnsetKeys($icvd_risk_items);
        $extras = CheckInfoExtra::getCheckInfoExtraByIds($check_info['check_id']);
        $result['reviewDate'] = $extras[$check_id]['extra']['icvd_report_snapshot']["next_check_suggestion"];
        foreach ($extras as $check_id => &$it) {
            if (!$it['extra']['icvd_report_snapshot']) {
                unset($extras[$check_id]);
                continue;
            }
            foreach ($it['extra']['icvd_report_snapshot']['icvd_risk'] as $risk => $risk_item) {
                if (in_array($risk, $unset_keys)) {
                    unset($it['extra']['icvd_report_snapshot']['icvd_risk'][$risk]);
                }
            }
        }
        //arteriosclerosis 动脉弹性改变风险
        //endocrine_disorder 内分泌失调
        //myocardial 心肌健康风险
        //cerebral 脑缺血风险
        //anemia 血气不足风险
        //brain_tumor 脑肿瘤风险
        if (empty($extras[$check_info['check_id']]['extra']['icvd_report_snapshot']['icvd_risk'])) {
            $this->setView($this->error_code_prefix . '05', '风险值还未产生', '');
            return FALSE;
        }
        $riskLevel = [];
        foreach ($extras[$check_info['check_id']]['extra']['icvd_report_snapshot']['icvd_risk'] as $k1 => $check_info1) {

            $data = [];
            if ($check_info1['key'] == 'arteriosclerosis' || $check_info1['key'] == 'endocrine_disorder' || $check_info1['key'] == 'myocardial' || $check_info1['key'] == 'cerebral' || $check_info1['key'] == 'anemia' || $check_info1['key'] == 'brain_tumor') {
                $data['name'] =  $check_info1['name'] . '(' . IcvdReport::getRiskStr($check_info1['risk_level']) . ')';
                $data['risk_level']  = $check_info1['risk_level'];
                if ($patient_data['gender'] == 1) {
                    $riskLevel[] = array_merge($data, self::WOMAN_AUDIO[$check_info1['key']][$check_info1['risk_level']]);
                } else {
                    $riskLevel[] = array_merge($data, self::MAN_AUDIO[$check_info1['key']][$check_info1['risk_level']]);
                }
            }
        }

        $risk_level = array_column($riskLevel, 'risk_level');
        array_multisort($risk_level, SORT_DESC, $riskLevel);
        $result['riskList'] = $riskLevel;
        $this->setView(0, 'success', $result);
    }

    private function _init()
    {
        $request = $this->request->REQUEST;
        if (!$request) {
            $this->setView($this->error_code_prefix . '01', '缺少参数', '');
            return FALSE;
        }
        if (!$request['en_check_id']) {
            $this->setView($this->error_code_prefix . '02', '缺少check_id', '');
            return FALSE;
        }
        return TRUE;
    }
}
