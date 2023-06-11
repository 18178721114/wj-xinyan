<?php
namespace Air\Unit\Did_consistency;

use Air\Package\Cache\RedisCache;
use Air\Package\Checklist\Helper\DBCheckInfoHelper;
use Air\Package\Checklist\Helper\RedisLock;
use Air\Package\Consistency\Helper\RedisTaskList;

class TriggerTask 
{
    public function __construct($arg)
    {
        $this->args = $arg;
    }

    /**
     *  php public/unit.php 'Did_consistency\TriggerTask' 2590514
     */ 
    public function run()
    {
        if (!$this->args[0]) {
          exit('no parameters');
        }
        // before
        $check_info = DBCheckInfoHelper::getLines(['check_id' => $this->args[0]])[0];
        $ext_json = json_decode($check_info['ext_json'], 1);
        $ret['before']['did_consistency'] = $ext_json['did_consistency'];
        $ret['before']['ReviewDone'] = RedisCache::getTTL($this->args[0], 'ReviewDone:DidConsistency:');
        $ret['before']['DetectionDone'] = RedisCache::getTTL($this->args[0], 'DetectionDone:DidConsistency:');
        $ret['before']['DidConsistency'] = RedisTaskList::getTask('DidConsistency');
        // trigger
        $sql = 'UPDATE ' . DBCheckInfoHelper::_TABLE_ . ' SET ext_json = json_remove(ext_json,"$.did_consistency") WHERE check_id = ' . $this->args[0];
        $ret['trigger']['did_consistency'] = DBCheckInfoHelper::updateDataBySql($sql);
        $ret['trigger']['ReviewDone'] = RedisCache::setCache($this->args[0], time(), 'ReviewDone:DidConsistency:', 3600);
        $ret['trigger']['DetectionDone'] = RedisCache::setCache($this->args[0], time(), 'DetectionDone:DidConsistency:', 3600);
        $ret['trigger']['DidConsistency'] = RedisTaskList::addTask('DidConsistency', $this->args[0], time());
        RedisLock::unLock('DidConsistency:' . $this->args[0]);
        // after
        $check_info = DBCheckInfoHelper::getLines(['check_id' => $this->args[0]])[0];
        $ext_json = json_decode($check_info['ext_json'], 1);
        $ret['after']['did_consistency'] = $ext_json['did_consistency'];
        $ret['after']['ReviewDone'] = RedisCache::getTTL($this->args[0], 'ReviewDone:DidConsistency:');
        $ret['after']['DetectionDone'] = RedisCache::getTTL($this->args[0], 'DetectionDone:DidConsistency:');
        $ret['after']['DidConsistency'] = RedisTaskList::getTask('DidConsistency');
        print_r($ret);exit();
    }
}