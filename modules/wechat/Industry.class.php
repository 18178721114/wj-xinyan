<?php

namespace Air\Modules\Wechat;

use Air\Package\Patient\PatientInfo;

class Industry extends \Air\Libs\Controller
{
    public $must_login = FALSE;

    public function run()
    {
        $ret = PatientInfo::getIndustryGroupTreeV2();
        $this->setView(0, 'success', $ret);
    }

    private function _init()
    {
        $request = $this->request->REQUEST;
        if (!$request) {
            $this->setView($this->error_code_prefix . '01', '缺少参数', '');
            return FALSE;
        }
        return TRUE;
    }
}
