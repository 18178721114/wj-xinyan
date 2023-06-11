<?php
namespace Air\MQ\Check_info_update;

class MQClearCacheConsumer extends \Phplib\Kafka\MQConsumer {
    const _CLUSTER_ = 'cluster';
    const _TOPIC_ = 'CHECK_INFO_UPDATE';
    
    public static $confs = array(
        'group.id' => 'CG001_CLEAR_CACHE',
    );

}
