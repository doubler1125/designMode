<?php
namespace Observer;
class ObserverPattern{};
/**
 * 观察者模式 (Observer)  又叫做发布-订阅（Publish/Subscribe）模式，是一种对象行为型模式。
 *
 * 含义：定义对象间的一种一对多依赖关系，使得每当一个对象状态发生改变时，其相关依赖对象皆得到通知并被自动更新。
 *
 * 组成：Subject: 抽象通知类，他把所有对观察者对象的引用保存在一个聚集里
 *      ConcreteSubject: 具体通知者，通常它包含有经常发生改变的数据，当它的状态发生改变时，向它的各个观察者发出通知
 *      Observer: 抽象观察者
 *      ConcreteObserver: 具体观察者，保存一个指向具体通知者的引用，它存储具体观察者的有关状态，这些状态需要和具体目标的状态保持一致。
 *
 * 优点：观察者模式可以实现表示层和数据逻辑层的分离，并定义了稳定的消息更新传递机制，抽象了更新接口，使得可以有各种各样不同的表示层作为具体观察者角色。
 *      观察者模式在观察目标和观察者之间建立一个抽象的耦合。
 *      观察者模式支持广播通信。
 *      观察者模式符合“开闭原则”的要求。
 * 缺点：如果一个观察目标对象有很多直接和间接的观察者的话，将所有的观察者都通知到会花费很多时间。
 *      如果在观察者和观察目标之间有循环依赖的话，观察目标会触发它们之间进行循环调用，可能导致系统崩溃。
 *      观察者模式没有相应的机制让观察者知道所观察的目标对象是怎么发生变化的，而仅仅只是知道观察目标发生了变化。
 *
 * 适用环境：（凡是涉及到一对一或者一对多的对象交互场景都可以使用观察者模式。）
 *      一个抽象模型有两个方面，其中一个方面依赖于另一个方面。将这些方面封装在独立的对象中使它们可以各自独立地改变和复用。
 *      一个对象的改变将导致其他一个或多个对象也发生改变，而不知道具体有多少对象将发生改变，可以降低对象之间的耦合度。
 *      一个对象必须通知其他对象，而并不知道这些对象是谁。
 *      需要在系统中创建一个触发链，A对象的行为将影响B对象，B对象的行为将影响C对象……，可以使用观察者模式创建一种链式触发机制。
 *
 * 拓展：事件委托，见下面实例
 *      如何让通知者可以通知不同类、不同方法呢？事件委托
 *      委托就是一种引用方法的类型。一旦为委托分配了方法，委托将与该方法具有完全相同的行为。
 *      委托可以看做是对函数的抽象，是函数的'类'，委托的实例代表一个具体的函数。
 *      一个委托可以搭载多个方法，所有方法被依次唤起。可以使得委托对象所搭载的方法并不需要属于同一个类。
 *
 */

//抽象通知类
abstract class Subject
{
    private $observers;

    public function __construct()
    {
        $this->observers = [];
    }

    //增加观察者
    public function Attach(Observer $observer)
    {
        $this->observers[] = $observer;
    }
    //移除观察者
    public function Detach(Observer $observer)
    {
        $newObservers = [];
        foreach ($this->observers as $obs) {
            if ($obs !== $observer) {
                $newObservers[] = $obs;
            }
        }
        $this->observers = $newObservers;
    }
    //通知
    public function Notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this); //要求观察者有update方法，可以通过事件委托来处理
        }
    }
}

//抽象观察者
abstract class Observer
{
    public abstract function update(); //此方法叫更新方法
}

//具体通知者
class ConcreteSubject extends Subject
{
    private $subjectState;

    public function getSubjectState()
    {
        return $this->subjectState;
    }
    public function setSubjectState($state)
    {
        $this->subjectState = $state;
    }
}

//具体观察者
class ConcreteObserver extends Observer
{
    private $name;
    private $observerState;
    private $subject;

    public function __construct(ConcreteSubject $subject, String $name)
    {
        $this->subject = $subject;
        $this->name = $name;
    }

    public function update()
    {
        $this->observerState = $this->subject->getSubjectState();
        echo sprintf('观察者%s的新状态是%s', $this->name, $this->observerState);
    }

    public function getSubject()
    {
        return $this->subject;
    }
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
}

//客户端
$s = new ConcreteSubject();
$s->Attach(new ConcreteObserver($s, 'X'));
$s->Attach(new ConcreteObserver($s, 'Y'));
$s->Attach(new ConcreteObserver($s, 'Z'));

$s->setSubjectState("ABC");
$s->Notify();

/**
 * 事件委托实例
 */

//使用时，添加 BaseHomeworkEventHandler 的子类如下面的 CommonEventHandler 即可，
//如下面的$homework的event_list属性有eventHandlers的key列表，每个eventHandler中都有onAssign和onFinish等事件执行的方法
//调用分发器Dispatcher的onAssign方法，通过$homework传入eventHandlers的key列表选出绑定的事件处理类去执行里面的onAssign会执行的方法
$homework = new \stdClass();
(new HomeworkEventDispatcher())->onAssign($homework); //当作业布置时触发
(new HomeworkEventDispatcher())->onFinish($homework); //当作业完成时触发

//作业事件分发器
final class HomeworkEventDispatcher
{
    //$eventType 事件类型
    const EVENT_ASSIGN = 'assign';      /** 作业布置事件 */
    const EVENT_FINISH = 'finish';      /** 作业完成事件 */
    /**
     * 当作业布置完成时调用
     */
    final public function onAssign($homework)
    {
        $this->dispatch(self::EVENT_ASSIGN, $homework, func_get_args());
    }
    /**
     * 当学生完成作业时调用
     */
    final public function onFinish($homeworkDo, $options = [])
    {
        $this->dispatch(self::EVENT_FINISH, $homeworkDo->homework, func_get_args());
    }

    //事件代理(处理)eventHandlers
    const HOOK_TYPE_COMMON = "common";              /** 通用事件事件代理 */
    const HOOK_TYPE_COMMON_URGE = 'common_urge';    /** 作业催促事件事件代理 */
    /**
     * 事件代理map
     */
    protected function hookMapper()
    {
        return [
            self::HOOK_TYPE_COMMON       => CommonEventHandler::class,
            self::HOOK_TYPE_COMMON_URGE  => CommonUrgeEventHandler::class,
        ];
    }
    /**
     * 事件分发
     */
    protected function dispatch($eventType, $homework, $params)
    {
        //根据作业内钩子类型组，获取对应的事件代理组
        $hookMapper = $this->hookMapper(); //map
        $hookList = $homework->hook_list;  //需要匹配的
        /** @var BaseHomeworkEventHandler[] $eventHandlers */
        $eventHandlers = [];
        foreach ($hookList as $hook) {
            if (isset($hookMapper[$hook])) {
                $handlerClass = $hookMapper[$hook];
                $eventHandlers[] = $handlerClass::instance(); //实例化匹配出来的事件代理组类
            }
        }

        $syncEvents = []; //同步执行
        $asyncEvents = [];//异步执行

        foreach ($eventHandlers as $eventHandler) {
            $items = $eventHandler->getItemsOnEvent($eventType); //选出$eventType时会执行的方法
            foreach ($items as $item) {
                if (true === $item->async) {
                    $asyncEvents[] = $item;
                }
                else {
                    $syncEvents[] = $item;
                }
            }
        }

        if (!empty($syncEvents)) {
            $this->run($syncEvents, $params);
        }

        if (!empty($asyncEvents)) {
            AmqpJobService::instance()->async([$this, 'run'], [$asyncEvents, $params], 'gaia.middleschool.queue' );
        }
    }

    /**
     * 运行事件组，同步或异步由 dispatch 控制
     *
     * @param $events
     * @param $params
     */
    public function run($events, $params)
    {
        array_walk($events, function (EventItem $eventItem) use ($params) {
            try {
                $eventItem->run(...$params);
            } catch (\Exception $e) {
                $this->setLastError($e);
            }
        });
    }
}

class CommonEventHandler extends BaseHomeworkEventHandler
{
    /**
     * 当作业布置的时候触发的方法
     *
     * @return EventItem[]
     */
    public function onAssignMethods(): array
    {
        return [
            EventItem::sync($this, 'set_students_homework'),
        ];
    }

    /**
     * 作业布置，绑定作业-学生关系
     */
    public function set_students_homework($homework)
    {
        $studentIds = $homework->student_ids;
        HomeworkStudentRelationModel::instance()->createRelations($homework, $studentIds);
    }

}

class CommonUrgeEventHandler extends BaseHomeworkEventHandler
{
    /**
     * 当作业完成的时候触发的方法
     *
     * @return EventItem[]
     */
    public function onFinishMethods(): array
    {
        return [
            EventItem::sync($this, 'finish_event_stop_urge'),
        ];

    }

    /**
     * 当作业开始的时候触发的方法
     *
     * @return EventItem[]
     */
    public function onStartMethods(): array
    {
        return [];
    }

    /**
     * 具体的执行方法
     * 学生完成作业，删掉对应催促
     */
    public function finish_event_stop_urge($homeworkDo)
    {
        $studentUrge = StudentHomeworkUrgeModel::instance()->get($homeworkDo->student, $homeworkDo->homework);
        $studentUrge->cancel(StudentHomeworkUrge::ON_FINISHED);
    }

}

/**
 * Class BaseHomeworkEventHandler
 * @package Services\Homework\EventHook
 */
abstract class BaseHomeworkEventHandler
{
    /**
     * 当作业完成的时候触发的方法
     *
     * @return EventItem[]
     */
    public function onFinishMethods() : array {return [];}

    /**
     * 当作业布置的时候触发的方法
     *
     * @return EventItem[]
     */
    public function onAssignMethods() : array {return [];}

    /**
     * 根据事件类型获取需要执行的事件节点
     *
     * @param $eventType
     * @return array|EventItem[]
     */
    final public function getItemsOnEvent($eventType)
    {
        switch ($eventType) {
            case HomeworkEventDispatcher::EVENT_ASSIGN:
                return $this->onAssignMethods();
            case HomeworkEventDispatcher::EVENT_FINISH :
                return $this->onFinishMethods();
            default :
                return [];
        }
    }

    /**
     * @param $method
     * @return EventItem
     */
    protected function async($method)
    {
        return EventItem::async($this, $method);
    }

    /**
     * @param $method
     * @return EventItem
     */
    protected function sync($method)
    {
        return EventItem::sync($this, $method);
    }
}

/**
 * Class EventItem
 * @package Services\Homework\EventHook
 */
class EventItem
{
    public $service;
    public $method;
    public $async;

    /**
     * EventItem constructor.
     * @param $service
     * @param $method
     * @param $async
     */
    protected function __construct($service, $method, $async)
    {
        $this->service = $service;
        $this->method = $method;
        $this->async = $async;
    }

    /**
     * 异步事件
     *
     * @param $service
     * @param $method
     * @return EventItem
     */
    public static function async($service, $method)
    {
        return new self($service, $method, true);
    }

    /**
     * 同步事件
     *
     * @param $service
     * @param $method
     * @return EventItem
     */
    public static function sync($service, $method)
    {
        return new self($service, $method, false);
    }

    /**
     * 运行时间，异步和同步在上层控制
     *
     * @param array ...$params
     */
    public function run(...$params)
    {
        $service = $this->service;
        $method = $this->method;
        $service->$method(...$params);
    }
}
