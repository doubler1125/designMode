<?php
namespace TemplateMethod;
class TemplateMethodPattern{};
/**
 * 模板方法模式（Template Method）
 *
 * 含义：定义一个操作中的算法的骨架，而将一些步骤延迟到子类中。
 *      模板方法使得子类可以不改变一个算法的结构即可重定义该算法的某些特定步骤。
 *
 * 组成：AbstractClass：抽象模板，定义并实现了一个模板方法，
 *      这个模板方法给出了一个逻辑的骨架，而逻辑的组成是一些相应的抽象操作，它们都是推迟到子类实现。
 *      ConcreteClass：实现类，实现父类模板方法中所定义的一个或多个抽象方法。
 *
 * 适用场景：一次性实现一个算法的不变的部分，并将可变的行为留给子类来实现；
 *      各子类中公共的行为应被提取出来并集中到一个公共父类中以避免代码重复；
 *      控制子类的扩展。
 */

abstract class AbstractClass
{
    public abstract function PrimitiveOperation1(); //一些抽象行为，放到子类去实现
    public abstract function PrimitiveOperation2();

    public function TemplateMethod() //模板方法，给出了一个逻辑的骨架，而逻辑的组成是一些相应的抽象操作，它们都是推迟到子类实现。
    {
        $this->PrimitiveOperation1();
        $this->PrimitiveOperation2();
    }
}

class ConcreteClassA extends AbstractClass
{
    public function PrimitiveOperation1()
    {
        print_r('具体类A方法1实现');
    }
    public function PrimitiveOperation2()
    {
        print_r('具体类A方法2实现');
    }
}

class ConcreteClassB extends AbstractClass
{
    public function PrimitiveOperation1()
    {
        print_r('具体类B方法1实现');
    }
    public function PrimitiveOperation2()
    {
        print_r('具体类B方法2实现');
    }
}

$c = new ConcreteClassA();
$c->TemplateMethod();
$c = new ConcreteClassB();
$c->TemplateMethod();