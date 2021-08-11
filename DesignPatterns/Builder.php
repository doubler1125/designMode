<?php
namespace Builder;
class BuilderPattern{};
/**
 * 建造者模式（Builder）又可以称为生成器模式
 *
 * 含义：将一个复杂对象的构建与它的表示分离，使得同样的构建过程可以创建不同的表示。
 *
 * 补充：建造者模式是一步一步创建一个复杂的对象，它允许用户只通过指定复杂对象的类型和内容就可以构建它们，用户不需要知道内部的具体构建细节。
 *      引入了一个指挥者类，该类的作用主要有两个：
 *        一方面它隔离了客户与生产过程；
 *        另一方面它负责控制产品的生成过程。指挥者针对抽象建造者编程，客户端只需要知道具体建造者的类型，即可通过指挥者类调用建造者的相关方法，返回一个完整的产品对象。
 *
 * 组成：Builder：抽象建造者，为创建Product对象的各个部件指定的抽象接口
 *      ConcreteBuilder：具体建造者，实现Builder接口，构造和装配各个部件
 *      Director：指挥者，构建一个使用Builder接口的对象
 *      Product：产品角色
 *
 * 优点：客户端不必知道产品内部组成的细节，将产品本身与产品的创建过程解耦。
 *      每一个具体建造者都相对独立，用户使用不同的具体建造者即可得到不同的产品对象
 *      可以更加精细地控制产品的创建过程。
 *      增加新的具体建造者无须修改原有类库的代码，指挥者类针对抽象建造者类编程，系统扩展方便，符合“开闭原则”。
 * 缺点：建造者模式所创建的产品一般具有较多的共同点，其组成部分相似，如果产品之间的差异性很大，则不适合使用建造者模式，因此其使用范围受到一定的限制。
 *      如果产品的内部变化复杂，可能会导致需要定义很多具体建造者类来实现这种变化，导致系统变得很庞大。
 *
 * 适用场景：用于创建一些复杂的对象，这些对象内部构建间的建造顺序通常是稳定的，但对象内部的建造通常面临着复杂的变化。
 *
 * 扩展：
 *      建造者模式的简化:
 *      省略抽象建造者角色：如果系统中只需要一个具体建造者的话，可以省略掉抽象建造者。
 *      省略指挥者角色：在具体建造者只有一个的情况下，如果抽象建造者角色已经被省略掉，那么还可以省略指挥者角色，让Builder角色扮演指挥者与建造者双重角色。
 *
 *      建造者模式与抽象工厂模式的比较:
 *      与抽象工厂模式相比， 建造者模式返回一个组装好的完整产品 ，而 抽象工厂模式返回一系列相关的产品，这些产品位于不同的产品等级结构，构成了一个产品族。
 *      在抽象工厂模式中，客户端实例化工厂类，然后调用工厂方法获取所需产品对象，而在建造者模式中，客户端可以不直接调用建造者的相关方法，而是通过指挥者类来指导如何生成对象，包括对象的组装过程和建造步骤，它侧重于一步步构造一个复杂对象，返回一个完整的对象。
 *      如果将抽象工厂模式看成 汽车配件生产工厂 ，生产一个产品族的产品，那么建造者模式就是一个 汽车组装工厂 ，通过对部件的组装可以返回一辆完整的汽车。
 */

//Product类，由多个部件组成
class Product
{
    private $parts = [];

    public function add($part) {
        $this->parts[] = $part;
    }

    public function show() {
        print_r('产品 创建 ---');
        foreach ($this->parts as $part) {
            print_r($part);
        }
    }
}

//Builder类
abstract class Builder
{
    public abstract function BuildPartA();
    public abstract function BuildPartB();
    public abstract function GetResult() : Product;
}

//ConcreteBuilder类
class ConcreteBuilder1 extends Builder
{
    private $product;

    function __construct()
    {
        $this->product = new Product();
    }

    public function BuildPartA()
    {
        $this->product->add('部件A');
    }

    public function BuildPartB()
    {
        $this->product->add('部件B');
    }

    public function GetResult(): Product
    {
        return $this->product;
    }
}
class ConcreteBuilder2 extends Builder
{
    private $product;

    function __construct()
    {
        $this->product = new Product();
    }

    public function BuildPartA()
    {
        $this->product->add('部件X');
    }

    public function BuildPartB()
    {
        $this->product->add('部件Y');
    }

    public function GetResult(): Product
    {
        return $this->product;
    }
}

//Director类
class Director
{
    public function Construct(Builder $builder)
    {
        $builder->BuildPartA(); //指挥建造过程
        $builder->BuildPartB();
    }
}

//客户端
$director = new Director();
$b1 = new ConcreteBuilder1();
$b2 = new ConcreteBuilder2();

$director->Construct($b1);
$p1 = $b1->GetResult();
$p1->show();

$director->Construct($b2);
$p2 = $b2->GetResult();
$p2->show();
