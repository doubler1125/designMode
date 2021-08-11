<?php
namespace Handler;
class ChainOfResponsibility {};
/**
 * 职责链模式（Chain Of Responsibility）
 *
 * 含义：使多个对象都有机会处理请求，从而避免请求的发送者和接收者之间的耦合关系。
 *      将这个对象连成一条链，并沿着这条链传递该请求，直到有一个对象处理它为止。
 *
 * 组成：Handler，定义一个处理请示的接口
 *      ConcreteHandler1、2.. 具体处理者类，处理它所负责的请求，可访问它的后继者，如果可处理该请求，就处理之，否则就将该请求转发给它的后继者
 *
 * 优点：1、降低耦合度。它将请求的发送者和接收者解耦。
 *      2、简化了对象。使得对象不需要知道链的结构。
 *      3、增强给对象指派职责的灵活性。通过改变链内的成员或者调动它们的次序，允许动态地新增或者删除责任。
 *      4、增加新的请求处理类很方便。
 * 缺点：1、不能保证请求一定被接收。
 *      2、系统性能将受到一定影响，而且在进行代码调试时不太方便，可能会造成循环调用。
 *      3、可能不容易观察运行时的特征，有碍于除错。
 *
 * 适用场景：1、有多个对象可以处理同一个请求，具体哪个对象处理该请求由运行时刻自动确定。
 *         2、在不明确指定接收者的情况下，向多个对象中的一个提交一个请求。
 *         3、可动态指定一组对象处理请求。
 *
 * 应用实例：1、红楼梦中的"击鼓传花"。
 *         2、JS 中的事件冒泡。
 *         3、JAVA WEB 中 Apache Tomcat 对 Encoding 的处理，Struts2 的拦截器，jsp servlet 的 Filter。
 */

abstract class Handler
{
    /** @var Handler */
    protected $successor;

    public function setSuccessor(Handler $handler)
    {
        $this->successor = $handler;
    }

    abstract function HandlerRequest(int $request);
}

class ConcreteHandler1 extends Handler
{
    public function HandlerRequest(int $request)
    {
        if (0 <= $request && $request < 10) {
            echo get_called_class() . '处理请求' . $request . PHP_EOL;
        }
        else if (!is_null($this->successor)) {
            $this->successor->HandlerRequest($request); //转移到下一位
        }
    }
}
class ConcreteHandler2 extends Handler
{
    public function HandlerRequest(int $request)
    {
        if (10 <= $request && $request < 20) {
            echo get_called_class() . '处理请求' . $request. PHP_EOL;
        }
        else if (!is_null($this->successor)) {
            $this->successor->HandlerRequest($request); //转移到下一位
        }
    }
}
class ConcreteHandler3 extends Handler
{
    public function HandlerRequest(int $request)
    {
        if (20 <= $request && $request < 30) {
            echo get_called_class() . '处理请求' . $request. PHP_EOL;
        }
        else if (!is_null($this->successor)) {
            $this->successor->HandlerRequest($request); //转移到下一位
        }
    }
}

$handler1 = new ConcreteHandler1();
$handler2 = new ConcreteHandler2();
$handler3 = new ConcreteHandler3();
$handler1->setSuccessor($handler2);
$handler2->setSuccessor($handler3);

$requests = [2, 5, 14, 22, 18, 3, 27 ,20];

foreach ($requests as $request) {
    $handler1->HandlerRequest($request);
}
