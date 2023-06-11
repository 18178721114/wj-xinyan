<?php

namespace Air\Config\Config;

class Kafka extends \Phplib\Config
{

    public function __construct()
    {
        $this->cluster = array(
            array('host' => '172.17.170.107', 'port' => '9092'),
            array('host' => '172.17.170.108', 'port' => '9092'),
            array('host' => '172.17.170.109', 'port' => '9092'),
        );
    }
}
