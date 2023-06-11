<?php

namespace Air\Unit\Did_consistency;

use Air\Package\Cache\RedisCache;
use Air\Package\Checklist\Helper\DBCheckInfoHelper;
use Air\Package\Checklist\Helper\RedisLock;
use Air\Package\Consistency\Helper\RedisTaskList;

/**
 * 脚本已无用
 */
class TaskStatus
{
    public function __construct($arg)
    {
        $this->args = $arg;
    }

    /**
     *  php public/unit.php 'Did_consistency\TaskStatus' 2590514
     *  status.did_consistency: null/0 待处理，1 开始处理一致性，2 处理一致性完成，3 已查看报告，不处理一致性
     *  status.ReviewDone: 阅片完成标记位剩余有效时间
     *  status.DidConsistency: Did一致性任务队列
     */
    public function run()
    {
        return false;
        if (!$this->args[0]) {
            exit('no parameters');
        }
        $check_info = DBCheckInfoHelper::getLines(['check_id' => $this->args[0]], true)[0];
        $ext_json = json_decode($check_info['ext_json'], 1);
        $ret['status']['did_consistency'] = $ext_json['did_consistency'];
        $ret['status']['ReviewDone'] = RedisCache::getTTL($this->args[0], 'ReviewDone:DidConsistency:');
        $ret['status']['DetectionDone'] = RedisCache::getTTL($this->args[0], 'DetectionDone:DidConsistency:');
        $ret['status']['DidConsistency'] = RedisTaskList::getTask('DidConsistency');
        print_r($ret);
        exit();
    }
}
