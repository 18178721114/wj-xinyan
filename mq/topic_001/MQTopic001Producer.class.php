<?php
namespace Air\MQ\Topic_001;

class MQTopic001Producer extends \Phplib\Kafka\MQProducer {
    const _CLUSTER_ = 'cluster';
    const _TOPIC_ = 'TOPIC_001';
    
    public static $confs = array();

}
