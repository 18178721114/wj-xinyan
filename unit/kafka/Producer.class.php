<?php
namespace Air\Unit\Kafka;

use Air\MQ\Topic_001\MQTopic001Producer;
use Air\Package\User\RandomName;

class Producer 
{
    public function __construct($arg)
    {
        $this->args = $arg;
        $this->hash = $arg[0];
        $this->producer = $arg[1] ? $arg[1] : 'script';
    }

    public function run()
    {
        $i = 0;
        while (true) {
            $message = [
                'index' => ++$i,
                'hash' => isset($this->args[0]) ? $this->hash : floor($i/3),
                'content' => RandomName::rand_name(),
                'time' => time(),
                'date' => date('Y-m-d H:i:s'),
                'producer' => $this->producer,
            ];
            $headers = [
                'header1' => 'hello header',
            ];
            $ret = MQTopic001Producer::produce($message, $message['hash'], true, ['headers' => $headers]);
            echo $i . '-' . $ret . "\n";
            sleep(1);
        }
    }

}