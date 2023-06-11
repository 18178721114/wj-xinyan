<?php
namespace Air\Unit\Kafka;

use Air\MQ\Topic_001\MQTopic001Consumer;

class Consumer 
{
    public function __construct($arg)
    {
        $this->args = $arg;
    }

    public function run()
    {
        while (true) {
            $ret = MQTopic001Consumer::consume();
            if (!is_int($ret)) {
                var_dump(
                  $ret->message,
                  $ret->headers,
                  $ret->len,
                  $ret->topic_name,
                  $ret->timestamp,
                  $ret->partition,
                  $ret->payload,
                  $ret->key,
                  $ret->offset,
                  $ret->opaque,
                );
            }
            sleep(3);
        }
    }
}