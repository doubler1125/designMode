<?php
namespace SimpleFactory;
use ReflectionClass;
use ReflectionException;

class SimpleFactoryPattern{};
/**
 * 简单工厂模式(也叫静态工厂模式）
 *
 * 定义：实质是由一个工厂类根据传入的参数，动态决定应该创建哪一个产品类（这些产品类继承自一个父类或接口）的实例。
 *      简单工厂模式的创建目标，所有创建的对象都是充当这个角色的某个具体类的实例。
 *
 * 补充：属于类创建型模式（同属于创建型模式的还有工厂方法模式，抽象工厂模式，单例模式，建造者模式）。
 *
 * 组成：简单工厂模式由三部分组成：具体工厂、具体产品和抽象产品
 *      1. 工厂类（Creator）角色：担任这个角色的是简单工厂模式的核心，含有与应用紧密相关的商业逻辑。工厂类在客户端的直接调用下创建产品对象。
 *      2. 抽象产品（AbstractProduct）角色：担任这个角色的类是由简单工厂模式所创建的对象的父类，或它们共同拥有的接口。
 *      3. 具体产品（ConcreteProduct）角色：简单工厂模式所创建的任何对象都是这个角色的实例。
 *
 * 优点：1、一个调用者想创建一个对象，只要知道其名称就可以了，降低了耦合度。
 *      2、扩展性高，如果想增加一个产品，只要扩展一个工厂类就可以。使得代码结构更加清晰。
 *      3、屏蔽产品的具体实现，调用者只关心产品的接口。
 *
 * 缺点：每次增加一个产品时，都需要增加一个具体类和对象实现工厂（这里可以使用反射机制来避免），使得系统中类的个数成倍增加，
 *      在一定程度上增加了系统的复杂度，同时也增加了系统具体类的依赖。所以对于简单对象来说，使用工厂模式反而增加了复杂度。
 *
 * 适用场景：1、 一个对象拥有很多子类。
 *         2、 创建某个对象时需要进行许多额外的操作。
 *         3、 系统后期需要经常扩展，它把对象实例化的任务交由实现类完成，扩展性好。
 */

/**
 * 实例：实现两个数字的加减乘除运算，实现起来很简单，当考虑到扩展运算，比如增加一个求根的运算，和复用的时候，
 *      我们发现简单工厂是一个很好的解决方案，我们不需要知道创建那个具体运算对象，我们只需要传入我们需要的运算符即可。
 */

//Operation运算类 (抽象产品)
class Operation
{
    private $_numberA = 0; //私有,通过方法访问
    private $_numberB = 0;

    public function getNumberA()
    {
        return $this->_numberA;
    }

    public function setNumberA($numberA)
    {
        $this->_numberA = $numberA;
    }

    public function getNumberB()
    {
        return $this->_numberB;
    }

    public function setNumberB($numberB)
    {
        $this->_numberB = $numberB;
    }

    public function getResult()
    {
        $result = 0;
        return $result;
    }

}

//加减乘除类 (具体产品)
class OperationAdd extends Operation
{
    public function getResult()
    {
        return $this->getNumberA() + $this->getNumberB();
    }
}
class OperationSub extends Operation
{
    public function getResult()
    {
        return $this->getNumberA() - $this->getNumberB();
    }
}
class OperationMul extends Operation
{
    public function getResult()
    {
        return $this->getNumberA() * $this->getNumberB();
    }
}
class OperationDiv extends Operation
{
    public function getResult()
    {
        if ($this->getNumberB() == 0) {
            throw new Exception("除数不能为0");
        }
        return $this->getNumberA() / $this->getNumberB();
    }
}

//简单运算工厂类 (工厂类)
class OperationFactory
{
    public function createOperate($operate)
    {
        $oper = null;
        switch ($operate) {
            case "+" :
                $oper = new OperationAdd();
                break;
            case "-" :
                $oper = new OperationSub();
                break;
            case "*" :
                $oper = new OperationMul();
                break;
            case "/" :
                $oper = new OperationDiv();
                break;
        }
        return $oper;
    }
}

//运行
$operation = (new OperationFactory())->createOperate('/');
$operation->setNumberA(100);
$operation->setNumberB(2);
echo $operation->getResult();

//反射
class FactoryBuilder
{
    public static function buildFactory($type) : BaseFactory
    {
        $className = __NAMESPACE__ . '\\' . self::getFactoryName($type).'Factory';
        if (!class_exists($className)) {
            $className = __NAMESPACE__ . '\\DefaultFactory';
        }

        try {
            $rClass = new ReflectionClass($className);
            $factory = $rClass->newInstanceWithoutConstructor();
        } catch (ReflectionException $e) {
            return new DefaultFactory();
        }
        return $factory;
    }

    protected static function getFactoryName($type)
    {
        return '';
    }
}