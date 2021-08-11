<?php
namespace FactoryMethod;
class FactoryMethodPattern{};

/**
 * 工厂方法模式（又称为工厂模式，也叫虚拟构造器(Virtual Constructor)模式或者多态工厂(Polymorphic Factory)模式，它属于类创建型模式）
 *
 * 含义：工厂父类负责定义创建产品对象的公共接口，而工厂子类则负责生成具体的产品对象，这样做的目的是将产品类的实例化操作延迟到工厂子类中完成，
 *      即通过工厂子类来确定究竟应该实例化哪一个具体产品类。
 *
 * 组成：1、Product：抽象产品
 *      2、ConcreteProduct：具体产品
 *      3、Factory：抽象工厂
 *      4、ConcreteFactory：具体工厂
 *
 * 优点：1、基于工厂角色和产品角色的多态性设计是工厂方法模式的关键。它能够使工厂可以自主确定创建何种产品对象，
 *      而如何创建这个对象的细节则完全封装在具体工厂内部。工厂方法模式之所以又被称为多态工厂模式，是因为所有的具体工厂类都具有同一抽象父类。
 *      2、在系统中加入新产品时，无须修改抽象工厂和抽象产品提供的接口，无须修改客户端，也无须修改其他的具体工厂和具体产品，而只要添加一个具体工厂和具体产品就可以了。
 *      这样，系统的可扩展性也就变得非常好，完全符合“开闭原则”。
 * 缺点：1、在添加新产品时，需要编写新的具体产品类，而且还要提供与之对应的具体工厂类，系统中类的个数将成对增加，在一定程度上增加了系统的复杂度，
 *      有更多的类需要编译和运行，会给系统带来一些额外的开销。
 *      2、由于考虑到系统的可扩展性，需要引入抽象层，在客户端代码中均使用抽象层进行定义，增加了系统的抽象性和理解难度，
 *      且在实现时可能需要用到DOM、反射等技术，增加了系统的实现难度。
 *
 * 适用场景：一个类不知道它所需要的对象的类；
 *          一个类通过其子类来指定创建哪个对象；
 *          将创建对象的任务委托给多个工厂子类中的某一个，客户端在使用时可以无须关心是哪一个工厂子类创建产品子类，需要时再动态指定。
 *
 * 补充：一般来说，工厂对象应当有一个抽象的父类型，如果工厂等级结构中只有一个具体工厂类的话，抽象工厂就可以省略，也将发生了退化。
 *      当只有一个具体工厂，在具体工厂中可以创建所有的产品对象，并且工厂方法设计为静态方法时，工厂方法模式就退化成简单工厂模式。
 */

//Operation运算类 (抽象产品)
/**
 * Class Operation
 * @package FactoryMethod
 * @property int $numberA
 * @property int $numberB
 */
class Operation
{
    private $numberA = 0; //私有,通过方法访问
    private $numberB = 0;

    public function __get($property)
    {
        if (isset($this->$property)) {
            return $this->$property;
        }
        return null;
    }

    public function __set($property, $value)
    {
        if (isset($this->$property)) {
            $this->$property = $value;
        }
        return null;
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
        return $this->numberA + $this->numberB;
    }
}
class OperationSub extends Operation
{
    public function getResult()
    {
        return $this->numberA - $this->numberB;
    }
}
class OperationMul extends Operation
{
    public function getResult()
    {
        return $this->numberA * $this->numberB;
    }
}
class OperationDiv extends Operation
{
    public function getResult()
    {
        if ($this->numberB == 0) {
            throw new Exception("除数不能为0");
        }
        return $this->numberA / $this->numberB;
    }
}

//抽象工厂
interface IFactory
{
    function createOperate();
}

//具体工厂
class AddFactory implements IFactory
{
    public function createOperate()
    {
        return new OperationAdd();
    }
}
class SubFactory implements IFactory
{
    public function createOperate()
    {
        return new OperationSub();
    }
}

//客户端
$factory = new AddFactory();
$operation = $factory->createOperate();
$operation->numberA = 100;
$operation->numberB = 2;
echo $operation->getResult();