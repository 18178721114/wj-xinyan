<?php
namespace Air\Modules\Wechat;

use Air\Package\Checklist\CheckInfo;
use Air\Package\User\Organizer;
use Air\Package\Wechat\WechatUserCheck;
use Air\Package\Wechat\Helper\RedisWechatPush;

class Push extends \Air\Libs\Controller
{
    public $must_login = TRUE;
    private $data = [];
    public function run()
    {
        $request = $this->request;
        $org_obj = new Organizer();
        $org = $org_obj->getOrganizerById($this->userSession['org_id']);

        $push_type = ($this->userSession['push_type'] == -1) ? $org['push_type'] : $this->userSession['push_type'];
        // if ($push_type != 2) {
        //     $this->setView(855555, '当前账号没有权限定时推送报告！', 0);
        //     return TRUE;
        // }
		$created = (string) $request->REQUEST['cstart'];
		$confirm = (int) $request->REQUEST['confirm'];
        if (!empty($created)) {
            $date = explode(',', $created);
            $params['created'] = [];
            foreach ($date as $key => $val) {
                $cc = explode(' ', $val);
                if ($key == 1) {
                    $this->data['created'][] = date('Y-m-d H:i:s', strtotime($cc[0] . ', ' . $cc[2] . ' ' . $cc[1] . ' ' . $cc[3] . ' ' . '23:59:59'));
                }
                else {
                    $this->data['created'][] = date('Y-m-d H:i:s', strtotime($cc[0] . ', ' . $cc[2] . ' ' . $cc[1] . ' ' . $cc[3] . ' ' . $cc[4]));
                }
            }
        }
        $user_id = $this->currentUserId();
		$this->data['submit_user_id'] = $user_id;
		$this->data['review_status'] = [CheckInfo::REVIEW_DONE, CheckInfo::REVIEW_DONE_NOBASE, CheckInfo::REVIEW_LAST_DONE_PK3, CheckInfo::REVIEW_LAST_DONE, CheckInfo::REVIEW_CHECK_DONE, CheckInfo::REVIEW_WAIT_FIRST, CheckInfo::WAIT_DETAIL, CheckInfo::REVIEW_WAIT_SENIOR_REVIEW]; 
        $obj = new CheckInfo();
        $result = $obj->getCheckRecords($this->data, 0, 9000, 1);
		if (!$result) {
            $this->setView(0, '', 0);
            return TRUE;
		}
		$check_ids = [];
		foreach ($result as $it) {
			$check_ids[] = $it['check_id'];
		}
		$to_pushs = WechatUserCheck::getToBePushWechatByCheckId($check_ids);
        //print_r($check_ids);
        //print_r($to_pushs);
        if (!$confirm) {
            $this->setView(0, '', count($to_pushs));
            return TRUE;
        }	
		foreach ($to_pushs as $ite) {
            $ret = RedisWechatPush::addCheckId($ite['check_id']);
		}
        $this->setView(0, 'success', $ret);
        return TRUE;
    }
}
