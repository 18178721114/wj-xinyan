<?php

namespace Air\Libs;

use \Phplib\Tools\Logger;
use \Air\Libs\Base\Utilities;
use Phplib\Kafka\MQMessage;

abstract class MQConsumerTasks
{
    /**
     * 指定 Consumer， 如 'Air\MQ\Topic_001\MQTopic001Consumer'
     */
    const MQ_CONSUMER = '';

    /**
     * 进程开始时间戳
     */
    protected $start_time = 0;

    /**
     * 消息非空时 usleep 时间，单位微秒
     */
    protected $sleep_time_us = 0;

    /**
     * 未获取到消息时  usleep 时间，单位微秒
     */
    protected $empty_sleep_time_us = 200000;

    /**
     * worker服务最长生命时间，单位秒
     * 脚本运行超过指定时间自动退出
     */
    protected $service_life_time = 115;

    /**
     * main服务最长生命时间，单位秒
     * 脚本运行超过指定时间自动退出
     */
    protected $main_service_life_time = 110;

    public function __construct($arg)
    {
        $this->args = $arg;
    }

    /**
     * 服务入口
     * 若 args[0] 为 worker 则调用 work() 消费消息，
     * 否则为 main 进程，调用 main() 管理 worker 进程，
     * main() 进程 采取奇偶数启动策略
     * 
     * @return null
     */
    public function run()
    {
        set_time_limit(0);
        $this->start_time = time();
        if ($this->args[0] == 'worker') {
            $this->work();
        } else if ($this->args[0] == 'recall') {
            //通过某一时间段获取到消息
            $this->recall();
        } else {
            if ($this->args[0] % 2 != intval(date('i')) % 2) {
                exit();
            }
            $this->main();
        }
    }

    /**
     * 管理 worker 进程
     * 获取 MQ_CONSUMER 指定 Consumer 对应的 topic 的 partition 的数量，
     * 用 shell_exec() 启动对应数量的 worker 进程，每个 worker 指定对应的 partition id，
     * worker 进程通过 flock 锁定，每个 partition 只运行一个对应的 worker，
     * 每秒钟执行一次，保证 Consumer 的可用性
     * 
     * @return null
     */
    protected function main()
    {
        $task_class = get_called_class();
        Logger::info($task_class . ' main service life start', 'consumer_timestracking');
        if (strpos('pre' . $task_class, 'Scripts')) {
            $main = 'script';
            $script = str_replace('\\', '\\\\', substr(strstr($task_class, 'Air\Scripts'), 12));
        } else {
            $main = 'unit';
            $script = str_replace('\\', '\\\\', substr(strstr($task_class, 'Air\Unit'), 9));
        }
        $partition_count = ($task_class::MQ_CONSUMER)::getPartitionCount();
        while (true) {
            if (time() - $this->start_time > $this->main_service_life_time) {
                Logger::info($task_class . ' main service life end', 'consumer_timestracking');
                exit();
            }
            for ($i = 0; $i < $partition_count; $i++) {
                $cmd = "flock -xn /tmp/" . strtolower(str_replace('\\', '_', $task_class))
                    . "_{$i}.lock -c '/usr/bin/php " . ROOT_PATH . "/public/{$main}.php {$script} worker {$i}' >/dev/null 2>&1 &";
                $ret = shell_exec($cmd);
            }
            sleep(1);
        }
    }

    /**
     * worker 进程，消费消息
     * 通过 MQ_CONSUMER 指定的Consumer 调用 consume() 消费消息，获取到消息后交给 handle() 处理
     * 
     * @return null
     */
    protected function work()
    {
        $task_class = get_called_class();
        $id = $this->args[1];
        Logger::info($task_class . ' worker ' . $id . ' service life start', 'consumer_timestracking');
        while (true) {
            if (time() - $this->start_time > $this->service_life_time) {
                Logger::info($task_class . ' worker ' . $id . ' service life end', 'consumer_timestracking');
                exit();
            }
            $ret = ($task_class::MQ_CONSUMER)::consume($this->service_life_time * 1000);
            if (is_object($ret) && get_class($ret) == 'Phplib\Kafka\MQMessage') {
                $this->handle($ret);
            } else {
                $ret = FALSE;
            }
            if (!$ret && $this->empty_sleep_time_us) {
                usleep($this->empty_sleep_time_us);
            } elseif ($this->sleep_time_us) {
                usleep($this->sleep_time_us);
            }
        }
    }

    /**
     * recall进程，消费消息
     * 根据某一时间段 回溯一段消息
     * 通过时间获取最开始和结束的offset  获取到消息后交给 handle() 处理
     * 
     * @return null
     */
    protected function recall()
    {
        $task_class = get_called_class();
        if (date('Y-m-d H:i:s', strtotime($this->args[1])) == $this->args[1]) {
            $start_offset_time = strtotime($this->args[1]) * 1000;
        } elseif (is_numeric($this->args[1]) && strlen($this->args[1]) == 10 ){
            $start_offset_time = $this->args[1] * 1000;
        } else{
            exit("Wrong start time! \n");
        }
        if (date('Y-m-d H:i:s', strtotime($this->args[2])) == $this->args[2]) {
            $end_offset_time = strtotime($this->args[2]) * 1000;
        } elseif (is_numeric($this->args[2]) && strlen($this->args[2]) == 10 ){
            $end_offset_time = $this->args[2] * 1000;
        } else{
            exit("Wrong end time! \n");
        }
        Logger::info($task_class . ' recall ' . $this->args[1] . ' ~ ' . $this->args[2] . ' service life start', 'consumer_timestracking');
        $partition_count = ($task_class::MQ_CONSUMER)::getPartitionCount();
        for ($i = 0; $i < $partition_count; $i++) {
            $start_offset = ($task_class::MQ_CONSUMER)::getOffsetsForTimes($i,$start_offset_time);
            $connect_time = 10;
            $connect_message_count = 0; // 单次连接计数
            $partition_message_count = 0;
            while (true) {
                $ret = ($task_class::MQ_CONSUMER)::consume($connect_time * 1000, ['partition' => $i, 'offset' => $start_offset]);
                if (is_int($ret) && in_array($ret, [($task_class::MQ_CONSUMER)::TIME_OUT])) {
                    // 超时了且单次连接计数 为0，则意味着当前 partition 没有消息了
                    if ($connect_message_count == 0) {
                        Logger::info('[recall] [partition ' . $i . ' no message] [ message count :' . $partition_message_count . ']' , ($task_class::MQ_CONSUMER)::_TOPIC_);
                        continue 2;
                    }
                    $connect_message_count = 0; // 超时就清空 单次连接计数
                } elseif (is_int($ret) && in_array($ret, [($task_class::MQ_CONSUMER)::EMPTY])) {
                    // 没有消息了
                    Logger::info('[recall] [partition ' . $i . ' empty] [ message count :' . $partition_message_count . ']' , ($task_class::MQ_CONSUMER)::_TOPIC_);
                    continue 2;
                } elseif (is_object($ret) && $ret->timestamp > $end_offset_time) {
                    // 超过结束时间
                    Logger::info('[recall] [partition ' . $i . ' end] [ message count :' . $partition_message_count . ']' , ($task_class::MQ_CONSUMER)::_TOPIC_);
                    continue 2;
                } elseif (is_object($ret) && get_class($ret) == 'Phplib\Kafka\MQMessage') {
                    $partition_message_count++;
                    $connect_message_count++;
                    $this->handle($ret);
                }
            }
        }
        Logger::info($task_class . ' recall ' . $this->args[1] . ' ~ ' . $this->args[2] . ' service life end', 'consumer_timestracking');
        exit("Complete! \n");
    }

    /**
     * 处理消息
     * 子类必须实现此方法，以处理消息
     * 
     * @param MQMessage|bool $ret MQMessage 实例，或为 false
     */
    abstract protected function handle($ret);
}
