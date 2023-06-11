<?php
namespace Air\MQ\Check_info_change;

class MQCheckInfoChangeConsumer extends \Phplib\Kafka\MQConsumer {
    const _CLUSTER_ = 'cluster';
    const _TOPIC_ = 'PANGU_CHECK_INFO_CHANGED';
    
    public static $confs = array(
        'group.id' => 'CG007_PANGU_CHECK_INFO_CHANGED',
    );

}
