<?php
namespace Air\MQ\Check_info_change;

class MQCheckInfoChangeProducer extends \Phplib\Kafka\MQProducer {
    const _CLUSTER_ = 'cluster';
    const _TOPIC_ = 'PANGU_CHECK_INFO_CHANGED';
    
    public static $confs = array();

}
