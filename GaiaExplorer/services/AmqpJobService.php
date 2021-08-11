<?php

class AmqpJobService extends BaseService implements \Gaia\Components\Queue\Recover
{
    /**
     * @param Job $job
     * @return int
     */
    public function publish($job)
    {
//        $client = GaiaApp::instance()->amqp();
        $client = ;
        try {
            if ($client->basicPublishWithDefaultExchange($job, $job->getQueue())) {
                $job->afterPublish();
            } else {
                $this->setLastError('消息发布失败', Api_Error::ERROR_CODE_AMQP_PUT, LOG_ERR);
                return false;
            }
        } catch (Exception $e) {
            $this->setLastError($e, Api_Error::ERROR_CODE_AMQP_PUT, LOG_ERR);
            return false;
        }
        return true;
    }

    public function async($function, $params, $queue)
    {
        return $this->publish(new FunctionCalledJob($queue, $function, $params));
    }

    /**
     * 落盘数据恢复函数
     * @param $messageBody
     * @return bool
     */
    public function afterRecover($messageBody)
    {
        /* @var Job $job */
        $client = GaiaApp::instance()->amqp();
        $job = $client->unserializeObject($messageBody);
        if (!$job) {
            $this->setLastError('消息解析失败', LOG_WARNING);
            return false;
        }

        try {
            $ok = $client->basicPublishMessageBody($messageBody, $job->getQueue());
            if (!$ok) {
                $this->setLastError('消息恢复失败', Api_Error::ERROR_CODE_AMQP_PUT, LOG_ERR);
                return false;
            }
        } catch (Exception $e) {
            $this->setLastError($e, Api_Error::ERROR_CODE_AMQP_PUT, LOG_ERR);
            return false;
        }
        return true;
    }
}