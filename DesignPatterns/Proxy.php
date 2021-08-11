<?php
namespace Proxy;
class ProxyPattern{};
/**
 * 代理模式（Proxy）
 *
 * 模式动机：在某些情况下，一个客户不想或者不能直接引用一个对 象，此时可以通过一个称之为“代理”的第三者来实现 间接引用。
 * 代理对象可以在客户端和目标对象之间起到 中介的作用，并且可以通过代理对象去掉客户不能看到 的内容和服务或者添加客户需要的额外服务。
 * 通过引入一个新的对象（如小图片和远程代理 对象）来实现对真实对象的操作或者将新的对 象作为真实对象的一个替身。
 *
 * 含义：为其他对象提供一种代理以控制对这个对象的访问。（是一种对象结构型模式）
 *
 * 组成：Subject: 抽象主题角色，声明了真实主题和代理主题的共同接口
 *      RealSubject: 真实主题角色，定义了代理角色所代表的真实对象，在真实主题角色中实现了真实的业务操作
 *      Proxy: 代理主题角色，内部包含对真实主题的引用，从而可以在任何时候操作真实主题对象
 *
 * 优点：代理模式能够协调调用者和被调用者，在一定程度上降低了系统的耦合度。
 *
 *      远程代理 使得客户端可以访问在远程机器上的对象，远程机器可能具有更好的计算性能与处理速度，可以快速响应并处理客户端请求。
 *      虚拟代理 通过使用一个小对象来代表一个大对象，可以减少系统资源的消耗，对系统进行优化并提高运行速度。
 *      保护代理 可以控制对真实对象的使用权限。
 *
 * 缺点：由于在客户端和真实主题之间增加了代理对象，因此有些类型的代理模式可能会造成请求的处理速度变慢。
 *      实现代理模式需要额外的工作，有些代理模式的实现非常复杂。
 *
 * 适用场景：
 *      远程(Remote)代理：为一个位于不同的地址空间的对象提供一个本地的代理对象，这个不同的地址空间可以是在同一台主机中，也可是在另一台主机中，远程代理又叫做大使(Ambassador)。
 *      虚拟(Virtual)代理：如果需要创建一个资源消耗较大的对象，先创建一个消耗相对较小的对象来表示，真实对象只在需要时才会被真正创建。
 *      Copy-on-Write代理：它是虚拟代理的一种，把复制（克隆）操作延迟到只有在客户端真正需要时才执行。一般来说，对象的深克隆是一个开销较大的操作，Copy-on-Write代理可以让这个操作延迟，只有对象被用到的时候才被克隆。
 *      保护(Protect or Access)代理：控制对一个对象的访问，可以给不同的用户提供不同级别的使用权限。
 *      缓冲(Cache)代理：为某一个目标操作的结果提供临时的存储空间，以便多个客户端可以共享这些结果。
 *      防火墙(Firewall)代理：保护目标不让恶意用户接近。
 *      同步化(Synchronization)代理：使几个用户能够同时使用一个对象而没有冲突。
 *      智能引用(Smart Reference)代理：当一个对象被引用时，提供一些额外的操作，如将此对象被调用的次数记录下来等。
 */

//Subject
abstract class Subject
{
    abstract function Request();
}

//RealSubject
class RealSubject extends Subject
{
    public function Request()
    {
        echo '真实的请求';
    }
}

//Proxy
class Proxy extends Subject
{
    /** @var Subject $realSubject */
    private $realSubject;

    public function Request()
    {
        if (is_null($this->realSubject)) {
            $this->realSubject = new RealSubject();
        }
        $this->realSubject->Request();
    }
}

//客户端
$proxy = new Proxy();
$proxy->Request();

//实例 -> 模拟数据库的分库分表场景

//IOrderMapper 模操作数据库的接口类
interface IOrderMapper
{
    public function insert(Order $order);
}
//OrderMapperImpl 模操作数据库的接口实现类
class OrderMapperImpl implements IOrderMapper
{
    public function insert(Order $order)
    {
        echo "添加Order成功";
        return true;
    }
}

//Order实例对象
class Order
{
    public $orderInfo;
    public $userId;
}
//IOrderService接口方法（代理对象接口）
interface IOrderService
{
    public function saveOrder(Order $order);
}
//OrderServiceImpl代理对象接口实现
class OrderServiceImpl implements IOrderService
{
    private $orderMapper;

    public function saveOrder(Order $order)
    {
        $this->orderMapper = new OrderMapperImpl();
        echo "Service调用mapper添加Order";
        $this->orderMapper->insert($order);
    }
}

//静态代理实现，具体选择数据库的连接由代理增强实现
class OrderServiceStaticProxy
{
    //目标对象
    private $orderService;

    public function saveOrder(Order $order)
    {
        //前置增强执行
        $this->beforeMethod($order);
        $this->orderService = new OrderServiceImpl();
        $result = $this->orderService->saveOrder($order);
        //后置增强执行
        $this->afterMethod();
        return $result;
    }

    private function beforeMethod(Order $order)
    {
        $userId = $order->userId;
        //获取DB路由策略
        $dbRouter = $userId % 2;
        echo "静态代理分配到[db" . $dbRouter . "] 处理数据";
        echo "静态代理 before code";
    }

    private function afterMethod()
    {
        echo "静态代理 after code";
    }
}
//客户端
$order = new Order();
$order->userId = 2;
$orderServiceStaticProxy = new OrderServiceStaticProxy();
$orderServiceStaticProxy->saveOrder($order);
//静态代理代码耦合性比较强，需要在具体的业务方法中手动调用。

//动态代理实现，通过生成代理类的方式执行增强方法，减少代码耦合性：
//todo InvocationHandler
class OrderServiceDynamicProxy
{
    private $target;

    public function __construct($target)
    {
        $this->target = $target;
    }

    //执行增强目标方法
    public function __call($method, $args)
    {
        $argObj = $args[0];
        $this->beforeMethod($argObj);
        $object = $this->target->$method($args);
        $this->afterMethod();
        return $object;
    }

    //生成代理对象
    public function bind()
    {
//        Class clazz = target.getClass();
//        return Proxy.newProxyInstance(clazz.getClassLoader(), clazz.getInterfaces(), this);
    }

    private function beforeMethod($o)
    {
        $userId = 0;
        echo "动态代理 before code";
        if ($o instanceof Order) {
            $order = $o;
            $userId = $order->userId;
        }
        $dbRouter = $userId % 2;
        echo "动态代理分配到[db" . $dbRouter . "] 处理数据";
    }

    private function afterMethod()
    {
        echo "动态代理 after code";
    }
}
//客户端
$order = new Order();
$order->userId = 2;
$orderServiceDynamicProxy = (new OrderServiceDynamicProxy(new OrderServiceImpl()))->bind();
$orderServiceDynamicProxy->saveOrder($order);