<?php

namespace Gaia\Components\Queue;

interface Recover
{
    /**
     * 队列落盘恢复函数
     * @param $messageBody
     * @return mixed
     */
    public function afterRecover($messageBody);
}