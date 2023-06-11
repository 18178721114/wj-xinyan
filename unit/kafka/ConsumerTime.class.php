<?php
namespace Air\Unit\Kafka;

use Air\MQ\Topic_001\MQTopic001Consumer;

class ConsumerTime 
{
    public function __construct($arg)
    {
        $this->args = $arg;
    }

    public function run()
    {
        while (true) {
            $ret = MQTopic001Consumer::getOffsetsForTimes(0, strtotime('2021-04-07 11:00:00') * 1000);
            if ($ret) {
              var_dump($ret);
            }
            sleep(3);
        }
    }
}
