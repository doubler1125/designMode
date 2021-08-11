<?php

use Gaia\Components\Queue\Queue;
use Gaia\Helpers\Fuse\ProviderInterface;

class AMQPClient extends Queue implements ProviderInterface
{
    /**
     * @var AMQPStreamConnection $connection
     * null ：未初始化
     * false ：初始化失败
     */
    private $connection = null;

    /**
     * @var AMQPChannel|InvocationWrapperHelper $channel
     * null ：未初始化
     * false ：初始化失败
     */
    private $channel = null;

    /* @var string[] $declaredQueues */
    private $declaredQueues = [];

    /* @var [] $_config : 默认配置 */
    protected $_config = [
        'host'               => '127.0.0.1',
        'port'               => 5672,
        'user'               => 'guest',
        'password'           => 'guest',
        'vhost'              => '/',
        'insist'             => false,
        'login_method'       => 'AMQPLAIN',
        'login_response'     => null,
        'locale'             => 'en_US',
        'connection_timeout' => 3.0,
        'read_write_timeout' => 3.0,
        'context'            => null,
        'keepalive'          => false,
        'heartbeat'          => 0
    ];

    /**
     * 建立新的 AMQP 连接 【参与熔断被invoker调用】
     *
     * @deprecated 由 invoker 调用
     * @return AMQPStreamConnection
     */
    public function initConnection()
    {
        $dsnInfo = $this->ensureDsnInfo();
        return new AMQPStreamConnection(
            $dsnInfo['host'],
            $dsnInfo['port'],
            $this->_config['user'],
            $this->_config['password'],
            $this->_config['vhost'],
            $this->_config['insist'],
            $this->_config['login_method'],
            $this->_config['login_response'],
            $this->_config['locale'],
            $this->_config['connection_timeout'],
            $this->_config['read_write_timeout'],
            $this->_config['context'],
            $this->_config['keepalive'],
            $this->_config['heartbeat']
        );
    }

    /**
     * 获取 channel 【参与熔断被invoker调用】
     * @deprecated 由 invoker 调用
     *
     * @return bool|AMQPChannel
     */
    public function initChannel()
    {
        $connection = $this->connect();
        if (!$connection) {
            return  false;
        }
        return $connection->channel();
    }

    /**
     * 发送消息 【参与熔断被invoker调用】
     *
     * @deprecated
     * @param $messageBody
     * @param $queue
     * @return bool
     */
    public function doBasicPublishMessageBody($messageBody, $queue)
    {
        $channel = $this->channel();
        if (!$channel) {
            $this->dumpMsg($messageBody, $queue);
            return false;
        }
        else {
            if (!in_array($queue, $this->declaredQueues)) {
                $channel->queue_declare($queue, false, true, false, false);
                $this->declaredQueues[] = $queue;
            }
            $message = new AMQPMessage($messageBody, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
            $channel->basic_publish($message, "", $queue);
        }
        return true;
    }

    /**
     * 获取 AMQP 连接 【参与熔断，直接调用】
     *
     * @return AMQPStreamConnection
     */
    public function connect()
    {
        if (null === $this->connection) {
            try {
                $this->connection = $this->getInvoker()->initConnection();
            } catch (\Exception $e) {
                $this->connection = false;
            }
        }
        return $this->connection;
    }

    /**
     * 获取 channel 【参与熔断，直接调用】
     *
     * @return AMQPChannel
     */
    public function channel()
    {
        if (null === $this->channel) {
            try {
                $this->channel = $this->getInvoker()->initChannel();
            } catch (\Exception $e) {
                $this->channel = false;
            }
        }
        return $this->channel;
    }

    /**
     * 发布消息 【参与熔断，直接调用】
     *
     * @param $queue
     * @return bool
     * @throws \Exception
     */
    public function basicPublishMessageBody($massageBody, $queue)
    {
        return  $this->getInvoker()->doBasicPublishMessageBody($massageBody, $queue);
    }

    /**
     * 发布消息 【参与熔断，直接调用】
     *
     * @param $object
     * @param $queue
     * @return bool
     * @throws \Exception
     */
    public function basicPublishWithDefaultExchange($object, $queue)
    {
        $messageBody = $this->serializeObject($object);

        try {
            return $this->basicPublishMessageBody($messageBody, $queue);
        }
        catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * Ensure dsn info
     *
     * @return array
     */
    public function ensureDsnInfo()
    {
        if (is_array($this->_config['host']) && !empty($this->_config['host'])) {
            $n = count($this->_config['host']);
            $key = $n > 1 ? (microtime(true) * 1000) % $n : 0;
            $info = parse_url($this->_config['host'][$key]);
            $host = \Api_Array::get($info, 'host', '127.0.0.1');
            $port = \Api_Array::get($info, 'port', 5672);
            return ['host' => $host, 'port' => $port];
        }
        return ['host' => $this->_config['host'], 'port' => $this->_config['port']];
    }

    /**
     * @param $obj
     * @return string
     */
    public function serializeObject($obj)
    {
        return serialize($obj);
    }

    public function unserializeObject($str)
    {
        return unserialize($str);
    }

    /** todo : 新版本迁移至 invokerMiddleware */
    /******************************************************************************************* */
    /**                                START Invoker & 熔断                                      */
    /******************************************************************************************* */
    use SimpleProvider;
    protected $invoker = null;

    /**
     * @return InvocationWrapperHelper|AMQPClient
     */
    protected function getInvoker()
    {
        if (null === $this->invoker) {
            $this->invoker =  new InvocationWrapperHelper(
                $this, 'amqp', [
                    InvocationWrapperHelper::OPTION_TIMEOUT    => 100,
                    InvocationWrapperHelper::OPTION_SHORT_NAME => 'AMQ',
                    InvocationWrapperHelper::OPTION_SHORT_NAME  => function ($name, $arguments) {
                        switch($name) {
                            case "initConnection" :
                                return $name;
                            case "initChannel" :
                                return $name;
                            case "doBasicPublishMessageBody" :
                                return "AMQP({$arguments[0]})::publish";
                            default:
                                return $name;
                        }
                    }
                ]
            );
        }
        return $this->invoker;
    }

    /**
     * 制定哪些方法参与熔断降级 【ProviderInterface】
     *
     * @return array
     */
    protected function fusableActions()
    {
        return [
            'initConnection'            => 1000,
            'initChannel'               => 1000,
            'doBasicPublishMessageBody' => 1000
        ];
    }

    /**
     * 当服务熔断状态下执行的逻辑与返回值 【ProviderInterface】
     *
     * @param null $action
     * @param $arguments
     * @return bool
     */
    public function &getReturnValueOnServiceClosed($action = null, $arguments = [])
    {
        $ret = false;

        switch($action) {
            case "initConnection" :
                break;
            case "initChannel" :
                break;
            case "doBasicPublishMessageBody" :
                $this->dumpMsg(...$arguments);
                break;
            default:
                break;
        }

        return $ret;
    }

    /**
     * 落盘
     *
     * @param $msg
     * @param $queue
     */
    protected function dumpMsg($msg, $queue)
    {
        $model = DumpedJobModel::instance();
        $model->dumpJob([
            'msg' => $msg,
            'queue' => $queue
        ], DumpedJobModel::JOB_TYPE_AMQP);

    }

    /**
     * 根据开放度判定是否本次是否提供服务 【ProviderInterface】
     *
     * @param $openness
     * @param $action
     * @param $arguments
     * @return true;
     */
    public function checkInvokeAbleByOpenness($openness, $action, $arguments = []) {
        return rand(0,100) <= $openness;
    }

    /******************************************************************************************* */
    /**                                   END Invoker & 熔断                                     */
    /******************************************************************************************* */
}