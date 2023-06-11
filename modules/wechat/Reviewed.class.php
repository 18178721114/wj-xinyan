<?php
/**
 * Created by PhpStorm.
 * User: qingyi
 * Date: 2017/10/19
 * Time: 下午3:24
 */

namespace Air\Modules\WeChat;

use Air\Libs\Xcrypt;
use Air\Package\Checklist\CheckInfo;

class Reviewed extends \Air\Libs\Controller
{
    public function run()
    {
        $request = $this->request;
        $check_id = (int) Xcrypt::decrypt($request->REQUEST['check_id']);
        if (empty($check_id) || !is_numeric($check_id)) {
            $this->setView(10003, '参数不正确', []);
            return FALSE;
        }
        $check_info = new CheckInfo();
        $result = $check_info->getCheckInfoSelfById($check_id);
        if ($result[0]['review_status'] >= 2) {
            $this->setView(0, '', ['finished' => 1]);
        } 
        else {
            $spend = time() - strtotime($result[0]['start_time']) - 30;
            if ($spend < 0) {
                $spend = 5;
            }
            $this->setView(0, '', ['finished' => 0, 'spend_time' => $spend]);
        }
    }
}
