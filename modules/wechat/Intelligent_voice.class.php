<?php

namespace Air\Modules\Wechat;

use Air\Package\Checklist\CheckInfo;
use Air\Package\User\Organizer;
use Air\Package\User\PatientCode;

class Intelligent_voice extends \Air\Libs\Controller
{
    public $must_login = FALSE;

    public function run()
    {
        $request = $this->request->REQUEST;
        if (!$this->_init()) {
            return false;
        }
        $this->setView(0, 'success', '');
    }

    private function _init()
    {
        $request = $this->request->REQUEST;
        if (!$request) {
            $this->setView('100001', gettext('缺少参数'), '');
            return FALSE;
        }
        if (!$request['pcode']) {
            $this->setView('100002', gettext('缺少参数'), '');
            return FALSE;
        }
        $pcode_item = PatientCode::getItemByPcode($request['pcode']); //查询上传过来的pcode 
        if (empty($pcode_item)) {
            $this->setView(100003, gettext('筛查码无效，请重新扫码获取。'), []);
            return false;
        }
        //判断机构 是否有权限
        $org = Organizer::getOrgByIds($pcode_item['org_id']);
        if (empty($org[$pcode_item['org_id']]['config']['show_intelligence_audio']) || $org[$pcode_item['org_id']]['config']['show_intelligence_audio'] != 1) {
            $this->setView(100004, gettext('没有权限预约'), []);
            return false;
        }
        // 判断是否生成检查单
        if (empty($pcode_item['check_id'])) {
            $this->setView(100005, gettext('照片未上传'), []);
            return false;
        }
        $check = new CheckInfo();
        $check->updateCheckInfo(['check_id' => $pcode_item['check_id'], 'ext_json' => ['show_intelligence_audio' => 1]]);
        return TRUE;
    }
}
