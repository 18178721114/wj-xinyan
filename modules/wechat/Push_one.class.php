<?php
namespace Air\Modules\Wechat;

use Air\Package\Checklist\CheckInfo;
use Air\Package\User\Organizer;
use Air\Package\Wechat\WechatUserCheck;
use Air\Package\Wechat\Helper\RedisWechatPush;

class Push_one extends \Air\Libs\Controller
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

		$check_id = (string) $request->REQUEST['check_id'];
        $force = isset($request->REQUEST['force']) ? intval($request->REQUEST['force']) : 0;
        if (empty($check_id)) {
            $this->setView(0, '', 0);
            return TRUE;
		}

        $user_id = $this->currentUserId();

        $obj = new CheckInfo();
        $info = $obj->getCheckInfoByIds($check_id);
        if (!$info) {
            $this->setView($this->error_code_prefix . '03', '检查单不存在');
            return FALSE;
        }
        $info = $info[0];

        if ($info['org_id'] != $this->userSession['org_id'] && intval($this->userSession['org_id']) != 1) {
            $this->setView(855555, '当前账号没有权限推送报告！', 0);
            return FALSE;
        }

        if ($force) {
            $ret = WechatUserCheck::updateStatus(0, 3, $check_id);
            $ret &= RedisWechatPush::addCheckId($check_id);
        }
        else {
            $to_pushs = WechatUserCheck::getToBePushWechatByCheckId([$check_id]);
            $ret = 0;
            foreach ($to_pushs as $ite) {
                $ret += RedisWechatPush::addCheckId($ite['check_id']);
            }
        }

        $this->setView(0, 'success', $ret);
        return TRUE;
    }
}
