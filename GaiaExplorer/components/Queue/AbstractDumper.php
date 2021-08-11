<?php

namespace Gaia\Components\Queue;

use Components\Component;
use Gaia\Services\MessageQueue\Job;

abstract class AbstractDumper extends Component
{

    /**
     * 队列消息落盘
     * @param String $messageBody 消息内容
     * @return bool
     */
    abstract public function output($messageBody);

    /**
     * 从落盘文件恢复，子类中自定$filter格式
     * @param mixed $filter
     * @param Recover $recover
     * @return mixed
     */
    abstract public function recover($filter, $recover);
}