<?php
namespace IteratorMethod;
class IteratorPattern{};
/**
 * 迭代器模式（Iterator）
 *
 * 含义：提供一种方法顺序访问一个聚合对象中各个元素，而又不暴露该对象的内部表示。
 *
 * 组成：1、Aggregats 聚集抽象类
 *      2、ConcreteAggregate 具体聚集类，继承Aggregats
 *      3、Iterator 迭代抽象类，用于定义得到开始对象、得到下一个对象、判断是否到结尾、当前对象等抽象方法，统一接口
 *      4、ConcreteIterator 具体迭代器类，继承Iterator，实现开始、下一个、是否结尾、当前对象等方法
 *
 * 优点：1、它支持以不同的方式遍历一个聚合对象。
 *      2、迭代器简化了聚合类。
 *      3、在同一个聚合上可以有多个遍历。
 *      4、在迭代器模式中，增加新的聚合类和迭代器类都很方便，无须修改原有代码。
 * 缺点：由于迭代器模式将存储数据和遍历数据的职责分离，增加新的聚合类需要对应增加新的迭代器类，类的个数成对增加，这在一定程度上增加了系统的复杂性。
 *
 * 适用场景：1、访问一个聚合对象的内容而无须暴露它的内部表示。 2、需要为聚合对象提供多种遍历方式。 3、为遍历不同的聚合结构提供一个统一的接口。
 *
 * 补充：高级编程语言C#、Java等本身已经把这个模式做在语言中了，
 *      PHP中是foreach，不需要知道集合对象是什么，就可以遍历所有的对象的循环工具。
 */

abstract Class Iterator
{
    public abstract function First();
    public abstract function Next();
    public abstract function IsDone(); //是否到结尾
    public abstract function CurrentItem();
}

abstract class Aggregate
{
    public abstract function CreteIterator();
}

class ConcreteIterator extends Iterator
{
    /** @var $aggregate ConcreteAggregate */
    private $aggregate;
    private $current = 0;

    public function __construct(ConcreteAggregate $concreteAggregate)
    {
        $this->aggregate = $concreteAggregate;
    }

    public function First()
    {
        return $this->aggregate->get(0);
    }
    public function Next()
    {
        $ret = null;
        $this->current += 1;
        if ($this->current < $this->aggregate->Count()) {
            $ret = $this->aggregate->get($this->current);
        }
        return $ret;
    }
    public function IsDone()
    {
        return $this->current >= $this->aggregate->Count() ? true : false;
    }
    public function CurrentItem()
    {
        return $this->aggregate->get($this->current);
    }
}

class ConcreteAggregate extends Aggregate
{
    private $items = [];

    public function CreteIterator()
    {
        return new ConcreteIterator($this);
    }

    public function Count() {
        return count($this->items);
    }

    public function get($name) {
        if (isset($this->items[$name])) {
            return $this->items[$name];
        }
        return null;
    }
    public function set($name, $value)
    {
        $this->items[$name] = $value;
    }
}

$concreteAggregate = new ConcreteAggregate();
$concreteAggregate->set(0, 'a');
$concreteAggregate->set(1, 'b');
$concreteAggregate->set(2, 'c');
$concreteAggregate->set(3, 'd');
$concreteAggregate->set(4, 'e');

//顺序遍历具体迭代器
$iterator = new ConcreteIterator($concreteAggregate);
$item = $iterator->First();
while (!$iterator->IsDone()) {
    echo $iterator->CurrentItem();
    $iterator->Next();
}

//逆向遍历具体迭代器同理继承抽象迭代器



