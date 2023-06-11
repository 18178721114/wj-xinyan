<?php
/**
 * Created by Hailong.
 */

namespace Air\Modules\Wechat;
use Air\Package\User\PatientCode;
use Air\Package\Patient\Patient;
use Air\Package\Wechat\Helper\RedisSearch;
use Air\Package\Wechat\WechatUserCheck;
use Air\Package\Checklist\CheckInfo;
use Air\Package\Checklist\CheckInfoExtra;
use Air\Package\Checklist\CheckInfoUtil;
use Air\Package\Checklist\Helper\RedisLock;
use Phplib\Tools\Logger;

class Search extends \Air\Libs\Controller
{
    public $must_login = false;
    public $general = 0;

    public function run()
    {
        $request = $this->request->REQUEST;
        $columns = ['en_open_id', 'name', 'birthday', 'uuid'];
        $params = [];
        $params['is_new'] = intval($request['is_new']);
        foreach ($columns as $column) {
            if (!$request[$column]) {
                $this->setView(102299, '缺少参数: ' . $column, []);
                return FALSE;
            }
            $params[$column] = trim($request[$column]);
        }
        $params['openid'] = \Air\Libs\Xcrypt::decrypt($params['en_open_id']);
        $params['family_name'] = mb_substr($params['name'], 0, 1);
        $exist_num = RedisSearch::getNum($params['openid']);
        Logger::error("uuid={$params['uuid']} event=search_{$params['is_new']} open_id={$params['openid']}", 'wechat_search');
        if ($exist_num >= RedisSearch::THRESHOLD) {
            $this->setView(102296, '抱歉，您已经尝试多次失败，我们查询不到报告，请联系现场工作人员。', []);
            return FALSE;
        }
        $patient = Patient::getPatientByUuid($params['uuid']);
        if (!$patient) {
            $this->setView(102291, '体检号不存在, 请咨询现场工作人员。', $params);
            return FALSE;
        }
        if ($patient['status'] != 2) {
            $this->setView(102292, '未查询到您有慧心瞳视网膜项目，请和护士确认您的项目是否是慧心瞳视网膜慢病筛查。', []);
            return FALSE;
        }
        if (date('Y-m-d', strtotime($params['birthday'])) != $patient['birthday'] ||
            $params['family_name'] != mb_substr($patient['name'], 0, 1)) {
            $remain = RedisSearch::monitor($params['openid']);
            if ($remain === FALSE) {
                $this->setView(102296, '抱歉，您已经尝试多次失败，我们查询不到报告，请联系现场工作人员。', []);
                return FALSE;
            }
            $this->setView(102293, '信息匹配失败，请确认输入的信息和您在爱康注册的个人信息完全一致，您今天还可以尝试'.$remain.'次查询!', []);
            return FALSE;
        }
        $check_info = CheckInfo::getOneFinishedCheckInfoByPatientId($patient['patient_id']);
        $obj = new CheckInfo();
        $obj->setAdmin(1);
        $check_detail_info = $obj->getCheckDetail($check_info[0]['check_id']);
        if (!$check_info && $patient['package_type'] == 0) {
            $this->setView(102294, '请先拍摄视网膜照片再查询。如果您已经拍摄，可能是网络原因，建议您5分钟后再试。', []);
            return FALSE;
        }
        $review_status = $check_info[0]['review_status'];
        $item = WechatUserCheck::addItem(['open_id' => $params['openid'], 'check_id' => $check_info[0]['check_id'], 'status' => 2]);
        Logger::error("uuid={$params['uuid']} event=success_{$params['is_new']} org_id={$check_info[0]['org_id']} package_type={$check_info[0]['package_type']}", 'wechat_search');
        //if ($check_info[0]['package_type'] > 0 && !in_array($review_status, [20, 40]) || $check_info[0]['package_type'] == 0 && !in_array($review_status, [2, 20, 40])) {
        if ($review_status < 2) {
            $this->setView(102299, '恭喜您查询成功，您的报告生成以后会收到一条微信推送，请注意查收微信消息。', []);
            return TRUE;
        }

        $check_info_extras = CheckInfoExtra::getCheckInfoExtraByIds($check_info[0]['check_id']);
        $check_info_extra = $check_info_extras[$check_info[0]['check_id']] ?? [];
        list($can_push_report, $can_not_push_report_reasons) = CheckInfoUtil::canPushReport($check_info[0], $check_info_extra);
        if (!$can_push_report) {
            $lock = RedisLock::lock('can_not_push_report_search_' . $check_info[0]['check_id'], 60);
            if ($lock) {
                $content = '未评估完成，原因【' . implode(',', $can_not_push_report_reasons) . '】 上传时间：' . $check_info[0]['created'] . ' 开始评估时间：' . $check_info[0]['start_time'];
                Logger::info($content, 'can_not_push_report', ['check_id' => $check_info[0]['check_id']]);
            };
            $this->setView(102299, '恭喜您查询成功，您的报告生成以后会收到一条微信推送，请注意查收微信消息。', []);
            return TRUE;
        }
        if (!in_array($review_status, [20, 30, 40])) {
            $this->setView(102299, '', '');
            return TRUE;    
        }
        $url = WechatUserCheck::getYingtongUrl($check_detail_info[0]);
        $url = WechatUserCheck::formatUserReportUrl($url);
        $url .= 'frm=wechat_search';
        $this->setView(0, '', ['report_url' => $url]);
        return TRUE;
    }
}
