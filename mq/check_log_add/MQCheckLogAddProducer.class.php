<?php
namespace Air\MQ\Check_log_add;

class MQCheckLogAddProducer extends \Phplib\Kafka\MQProducer
{
    const _CLUSTER_ = 'cluster';
    const _TOPIC_ = 'CHECK_LOG_ADD';
    
    public static $confs = array();

    public static function syncCheckLog($check_id, $check_log_info, $flush = true)
    {
        return self::produce($check_log_info, $check_id, $flush);
    }
}
