<?php
namespace Mediator;
class MediatorPattern{};
/**
 * 中介者模式（Mediator）
 *
 * 含义：用一个中介对象来封装一系列的对象交互，中介者使各对象不需要显式地相互引用，从而使其耦合松散，而且可以独立地改变它们之间的交互。
 *
 * 组成：Mediator: 抽象中介者，定义了同事对象到中介者对象的接口
 *      ConcreteMediator: 具体中介者，它需要知道所有具体同事类，并从具体同事接收消息，向具体同事对象发出命令
 *      Colleague: 抽象同事类
 *      ConcreteColleague: 具体同事类，每个具体同事只知道自己的行为，而不了解其他同事类的情况，但它们却都认识中介者对象
 *
 * 优点：1、降低了类的复杂度，将一对多转化成了一对一。 2、各个类之间的解耦。 3、符合迪米特原则。
 * 缺点：在具体中介者类中包含了同事之间的交互细节，可能会导致具体中介者类非常复杂，使得系统难以维护。
 *
 * 适用场景：系统中对象之间存在复杂的引用关系，产生的相互依赖关系结构混乱且难以理解。
 *         一个对象由于引用了其他很多对象并且直接和这些对象通信，导致难以复用该对象。
 *         想通过一个中间类来封装多个类中的行为，而又不想生成太多的子类。可以通过引入中介者类来实现，在中介者中定义对象。
 *         交互的公共行为，如果需要改变行为则可以增加新的中介者类。
 *
 * 应用举例：1、中国加入 WTO 之前是各个国家相互贸易，结构复杂，现在是各个国家通过 WTO 来互相贸易。
 *         2、机场调度系统。 3、MVC 框架，其中C（控制器）就是 M（模型）和 V（视图）的中介者。
 *         4、虚拟聊天室
 */

abstract class Mediator
{
    abstract function send(String $message, Colleague $colleague);  //发送信息是通过中介者
}

abstract class Colleague
{
    protected $mediator;

    public function __construct(Mediator $mediator) //得到中介者对象
    {
        $this->mediator = $mediator;
    }
}

/**
 * Class ConcreteMediator
 * @package Mediator
 * @property ConcreteColleague1 $colleague1
 * @property ConcreteColleague2 $colleague2
 */
class ConcreteMediator extends Mediator
{
    private $colleague1 = '';
    private $colleague2 = '';

    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        }
    }

    public function send(String $message, Colleague $colleague) //重写发送信息的方法，根据对象做出选择判断，通知对象
    {
        if ($colleague === $this->colleague1) {
            $this->colleague2->notify($message);
        }
        else {
            $this->colleague1->notify($message);
        }
    }
}

class ConcreteColleague1 extends Colleague
{
    public function send(String $message)
    {
        $this->mediator->send($message, $this); //具体同事类发送信息是通过中介者发送出去的
    }

    public function notify(String $message)
    {
        echo '同事1得到信息：'.$message;
    }
}
class ConcreteColleague2 extends Colleague
{
    public function send(String $message)
    {
        $this->mediator->send($message, $this);
    }

    public function notify(String $message)
    {
        echo '同事2得到信息：'.$message;
    }
}

$concreteMediator = new ConcreteMediator();
$concreteColleague1 = new ConcreteColleague1($concreteMediator); //让两个具体同事类认识中介者对象
$concreteColleague2 = new ConcreteColleague2($concreteMediator);

$concreteMediator->colleague1 = $concreteColleague1; //让中介者认识各个具体同事类对象
$concreteMediator->colleague2 = $concreteColleague2;

$concreteColleague1->send('吃过饭了吗');
$concreteColleague2->send('没有呢，你打算请客？');