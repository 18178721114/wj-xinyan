<?php
namespace Air\MQ\Check_info_add;

class MQCheckInfoAddProducer extends \Phplib\Kafka\MQProducer {
    const _CLUSTER_ = 'cluster';
    const _TOPIC_ = 'CHECK_INFO_ADD';
    
    public static $confs = array();

}
