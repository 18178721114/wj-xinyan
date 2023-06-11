<?php
namespace Air\MQ\Check_info_update;

class MQCheckInfoUpdateProducer extends \Phplib\Kafka\MQProducer {
    const _CLUSTER_ = 'cluster';
    const _TOPIC_ = 'CHECK_INFO_UPDATE';
    
    public static $confs = array();

}
