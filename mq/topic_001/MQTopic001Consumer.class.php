<?php
namespace Air\MQ\Topic_001;

class MQTopic001Consumer extends \Phplib\Kafka\MQConsumer {
    const _CLUSTER_ = 'cluster';
    const _TOPIC_ = 'TOPIC_001';
    
    public static $confs = array(
        'group.id' => 'TEST_TOPIC_001_CG001',
    );

}
