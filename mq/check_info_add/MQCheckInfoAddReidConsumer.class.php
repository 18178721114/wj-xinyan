<?php
namespace Air\MQ\Check_info_add;

class MQCheckInfoAddReidConsumer extends \Phplib\Kafka\MQConsumer {
    const _CLUSTER_ = 'cluster';
    const _TOPIC_ = 'CHECK_INFO_ADD';
    
    public static $confs = array(
        'group.id' => 'CG004_CHEK_INFO_ADD_REID',
    );

}
