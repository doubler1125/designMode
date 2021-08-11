<?php
namespace AbstractFactory;
class AbstractFactoryPattern{};
/**
 * 抽象工厂模式（Abstract Factory）
 *
 * 含义：是一种为访问类提供一个创建一组相关或相互依赖对象的接口，且访问类无需指定所要产品的具体类就能得到同族的不同等级的产品的模式结构。
 *
 * 由来：工厂方法模式引入工厂等级结构，解决了简单工厂模式中工厂类职责过重的问题，
 *      但由于工厂方法模式中每个工厂只创建一类具体类的对象，这将会导致系统当中的工厂类过多，这势必会增加系统的开销。
 *      此时，我们可以考虑将一些相关的具体类组成一个“具体类族”，由同一个工厂来统一生产，这就是我们本文要说的“抽象工厂模式”的基本思想。
 *
 * 组成：AbstractFactory：抽象工厂，提供了创建产品的接口，它包含多个创建产品的方法newProduct()，可以创建多个不同等级的产品
 *      ConcreteFactory：具体工厂，实现抽象工厂中的多个抽象方法，完成具体产品的创建
 *      AbstractProduct：抽象产品，定了产品的规范，描述了产品的主要特性和功能，抽象工厂模式有多个抽象产品
 *      Product：具体产品，实现了抽象产品角色多定义的接口，由具体工厂来创建，它同具体工厂之间是多对一的关系
 *
 * 补充：产品等级结构 ：产品等级结构即产品的继承结构，如一个抽象类是电视机，其子类有海尔电视机、海信电视机、TCL电视机，则抽象电视机与具体品牌的电视机之间构成了一个产品等级结构，抽象电视机是父类，而具体品牌的电视机是其子类。
 *      产品族 ：在抽象工厂模式中，产品族是指由同一个工厂生产的，位于不同产品等级结构中的一组产品，如海尔电器工厂生产的海尔电视机、海尔电冰箱，海尔电视机位于电视机产品等级结构中，海尔电冰箱位于电冰箱产品等级结构中。
 *
 * 优点：隔离了具体类的生成，使得客户并不需要知道什么被创建。由于这种隔离，更换一个具体工厂就变得相对容易。
 *      当一个产品族中的多个对象被设计成一起工作时，它能够保证客户端始终只使用同一个产品族中的对象。这对一些需要根据当前环境来决定其行为的软件系统来说，是一种非常实用的设计模式。
 *      增加新的具体工厂和产品族很方便，无须修改已有系统，符合“开闭原则”。
 * 缺点：开闭原则的倾斜性（增加新的工厂和产品族容易，增加新的产品等级结构麻烦）。
 *
 * 适用场景：一个系统不应当依赖于产品类实例如何被创建、组合和表达的细节；
 *      系统中有多于一个的产品族，而每次只使用其中某一产品族；
 *      属于同一个产品族的产品将在一起使用；
 *      系统提供一个产品类的库，所有的产品以同样的接口出现，从而使客户端不依赖于具体实现。
 *
 * 应用实例：见下方抽象工厂与简单工厂、反射的配合使用
 */

//情景假设：假设现在有一个奥迪造车工厂，生产的车系有Q3,Q5,Q7三种不同型号但同属于Q系列的轿车，虽然同属于Q系列轿车，但三者车型的零部件差别还是很大。

//抽象汽车工厂类,需要生产发动机，轮胎，制动系统这3种零部件
use ReflectionClass;
use ReflectionException;

abstract class CarFactory
{
    //生产轮胎
    abstract function createTire();
    //生产发动机
    abstract function createEngine();
    //生产制动系统
    abstract function createBrake();
}

//为每种零部件定义一个接口，并分别创建两个不同的实现类表示不同的零部件
interface ITire
{
    public function tire();
};
class NormalTire implements ITire
{
    public function tire()
    {
        print_r('普通轮胎');
    }
}
class SUVTire implements ITire
{
    public function tire()
    {
        print_r('SUV轮胎');
    }
}

interface IEngine
{
    public function engine();
};
class DomesticEngine implements IEngine
{
    public function engine()
    {
        print_r('国产发动机');
    }
}
class ImportEngine implements IEngine
{
    public function engine()
    {
        print_r('进口发动机');
    }
}

interface IBrake
{
    public function brake();
};
class NormalBrake implements IBrake
{
    public function brake()
    {
        print_r('普通制动系统');
    }
}
class SeniorBrake implements IBrake
{
    public function brake()
    {
        print_r('高级制动系统');
    }
}

//对于具体生产工厂Q3，Q7，生产的零部件均不相同
class Q3Factory extends CarFactory
{
    //生产轮胎
    public function createTire() {
        return new NormalTire();
    }
    //生产发动机
    public function createEngine() {
        return new DomesticEngine();
    }
    //生产制动系统
    public function createBrake() {
        return new NormalBrake();
    }
}
class Q7Factory extends CarFactory
{
    //生产轮胎
    public function createTire() {
        return new SUVTire();
    }
    //生产发动机
    public function createEngine() {
        return new ImportEngine();
    }
    //生产制动系统
    public function createBrake() {
        return new SeniorBrake();
    }
}

//客户端
//构造一个生产Q3的工厂
$Q3Factory = new Q3Factory();
$Q3Factory->createTire()->tire();
$Q3Factory->createEngine()->engine();
$Q3Factory->createBrake()->brake();

echo PHP_EOL;

//构造一个生产Q7的工厂
$Q3Factory = new Q7Factory();
$Q3Factory->createTire()->tire();
$Q3Factory->createEngine()->engine();
$Q3Factory->createBrake()->brake();

//抽象工厂与简单工厂、反射的配合使用
class ReflectionFactory extends CarFactory
{
    private $car = '';

    public function __construct($car)
    {
        $this->car = $car; //可走配置文件
    }

    public function createTire() {
        $className = __NAMESPACE__ . '\\' . $this->car . 'Tire';
        try {
            $class = new ReflectionClass($className);
            $tire = $class->newInstance();
        } catch (ReflectionException $Exception) {
            throw new \InvalidArgumentException('暂不支持的类型');
        }
        return $tire;
    }
    public function createEngine() {
        $className = __NAMESPACE__ . '\\' . $this->car . 'Engine';
        try {
            $class = new ReflectionClass($className);
            $engine = $class->newInstance();
        } catch (ReflectionException $Exception) {
            throw new \InvalidArgumentException('暂不支持的类型');
        }
        return $engine;
    }
    public function createBrake() {
        $className = __NAMESPACE__ . '\\' . $this->car . 'Brake';
        try {
            $class = new ReflectionClass($className);
            $brake = $class->newInstance();
        } catch (ReflectionException $Exception) {
            throw new \InvalidArgumentException('暂不支持的类型');
        }
        return $brake;
    }
}
