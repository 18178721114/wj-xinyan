<?php
namespace Air\Unit\Kafka;

class ConsumerTasks extends \Air\Libs\MQConsumerTasks
{
    const MQ_CONSUMER = 'Air\MQ\Topic_001\MQTopic001Consumer';
    
    protected $sleep_time_ms = 2000000;
    protected $empty_sleep_time_ms = 3000000;
    protected $service_life_time = 115;
    protected $main_service_life_time = 110;

    protected function handle($message) {
        if ($message) {
            var_dump(
              $message->message,
              $message->headers,
              $message->len,
              $message->topic_name,
              $message->timestamp,
              $message->partition,
              $message->payload,
              $message->key,
              $message->offset,
              $message->opaque
            );
        }
    }
}