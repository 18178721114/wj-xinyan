<?php
namespace Air\MQ\Check_info_add;

class MQCheckInfoAddMonitorConsumer extends \Phplib\Kafka\MQConsumer {
    const _CLUSTER_ = 'cluster';
    const _TOPIC_ = 'CHECK_INFO_ADD';
    
    public static $confs = array(
        'group.id' => 'CG006_CHECK_INFO_ADD_MONITOR',
    );

}
