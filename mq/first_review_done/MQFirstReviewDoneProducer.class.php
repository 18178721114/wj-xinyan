<?php

namespace Air\MQ\First_review_done;

class MQFirstReviewDoneProducer extends \Phplib\Kafka\MQProducer
{
    const _CLUSTER_ = 'cluster';
    const _TOPIC_ = 'PANGU_FIRST_REIVIEW_DONE';

    public static $confs = array();
}
