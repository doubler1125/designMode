<?php
namespace Definition;
class DefinitionPattern{};
/**
 * 装饰模式（Decorator）
 *
 * 模式动机：一般有两种方式可以实现给一个类或对象增加行为：
 * 继承机制，使用继承机制是给现有类添加功能的一种有效途径，通过继承一个现有类可以使得子类在拥有自身方法的同时还拥有父类的方法。但是这种方法是静态的，用户不能控制增加行为的方式和时机。
 * 关联机制，即将一个类的对象嵌入另一个对象中，由另一个对象来决定是否调用嵌入对象的行为以便扩展自己的行为，我们称这个嵌入的对象为装饰器(Decorator)。
 *
 * 含义：动态地给一个对象增加一些额外的职责，就增加对象功能来说，装饰模式比生成子类实现更为灵活。
 *      其别名也可以称为包装器(Wrapper)，与适配器模式的别名相同，但它们适用于不同的场合。
 *
 * 补充：允许向一个现有的对象添加新的功能，同时又不改变其结构。这种类型的设计模式属于结构型模式，它是作为现有的类的一个包装。
 *      装饰模式以对客户端透明的方式扩展对象的功能，是继承关系的一个替代方案。
 *      大多数的装饰模式实际上是半透明的装饰模式，这样的装饰模式也称做半装饰、半适配器模式。
 *
 * 组成：Component（抽象构件）给出一个抽象接口，以规范准备接收附加责任的对象。
 *      ConcreteComponent（具体构件） 定义一个将要接收附加责任的类。
 *      Decorator（抽象装饰类） 持有一个构件(Component)对象的实例，并定义一个与抽象构件接口一致的接口。
 *      ConcreteDecorator（具体装饰类）负责给构件对象“贴上”附加的责任。
 *
 * 优点：装饰模式与继承关系的目的都是要扩展对象的功能，但是装饰模式可以提供比继承更多的灵活性。
 *      装饰模式允许系统动态决定“贴上”一个需要的“装饰”，或者除掉一个不需要的“装饰”。继承关系则不同，继承关系是静态的，它在系统运行前就决定了。
 *      通过使用不同的具体装饰类以及这些装饰类的排列组合，设计师可以创造出很多不同行为的组合。
 * 缺点：使用装饰模式会产生比使用继承关系更多的对象。更多的对象会使得查错变得困难，特别是这些对象看上去都很相像。
 *
 * 适用场景：
 *      1、在不影响其他对象的情况下，以动态、透明的方式给单个对象添加方法。
 *      2、需要动态的给一个对象增加功能，这些功能可以再动态地撤销。
 *      3、当不能采用继承的方式对系统进行扩充或者采用继承不利于系统扩展和维护时。
 *      不能采用继承的情况主要有两类：第一类是系统中存在大量独立的扩展，为支持每一种组合将产生大量的子类，使得子类数目呈爆炸性增长；
 *      第二类是因为类定义不能继承（如final类）
 */

//Component类
abstract class Component
{
    public abstract function  Operation();
}

//ConcreteComponent类
class ConcreteComponent extends Component
{
    public function Operation()
    {
        echo '具体对象的操作' . PHP_EOL;
    }
}

//Decorator类
abstract class Decorator extends Component
{
    /** @var  Component $component*/
    protected $component;

    public function SetComponent(Component $component)
    {
        $this->component = $component;
    }

    //重写Operation(),实际执行的是Component的Operation
    public function Operation()
    {
        if (!is_null($this->component)) {
            $this->component->Operation();
        }
    }
}

//ConcreteDecoratorA类
class  ConcreteDecoratorA extends Decorator
{
    //本类的独有功能，以区别于ConcreteDecoratorB
    private $addedState;

    //首先运行原Component的Operation()，再执行本类的功能，如addedState，相当于对原Component进行了装饰
    public function Operation()
    {
        parent::Operation();
        $this->addedState = "New State";
        echo "具体装饰对象A的操作" . PHP_EOL;
    }
}
//ConcreteDecoratorB类
class  ConcreteDecoratorB extends Decorator
{
    //本类独有的方法，以区别于ConcreteDecoratorA
    private function addedBehavior()
    {

    }


    //首先运行原Component的Operation()，再执行本类的功能，如AddedBehavior，相当于对原Component进行了装饰
    public function Operation()
    {
        parent::Operation();
        $this->AddedBehavior();
        echo "具体装饰对象B的操作" . PHP_EOL;
    }
}

//客户端代码
$c = new ConcreteComponent();
$a = new ConcreteDecoratorA();
$b = new ConcreteDecoratorB();

//装饰的方法是：先用ConcreteComponent实例化对象$c,然后用ConcreteDecoratorA的实例化对象$a来包装$c,
//再用ConcreteDecoratorB的对象$b来包装$a，最终执行$b的Operation()
$a->SetComponent($c);
$b->SetComponent($a);
$b->Operation();

//可见：装饰模式是利用SetComponent来对对象进行包装的。
//这样每个装饰对象的实现就和如何使用这个对象分离开了，每个装饰对象只关心自己的功能，不需要关心如何被添加到对象链当中。

//变通：如果只有一个ConcreteComponent类而没有抽象的Component类，那么Decorator类可以是ConcreteComponent的一个子类。
//如果只有一个ConcreteDecorator类，那么就没有必要建立一个单独Decorator类，而可以把Decorator和ConcreteDecorator的责任合并成一个类。

//实例：
//咖啡是一种饮料，咖啡的本质是咖啡豆+水磨出来的。咖啡店现在要卖各种口味的咖啡，如果不使用装饰模式，那么在销售系统中，
//各种不一样的咖啡都要产生一个类，如果有4中咖啡豆，5种口味，那么将要产生至少20个类（不包括混合口味），非常麻烦。
//使用了装饰模式，只需要11个类即可生产任意口味咖啡（包括混合口味）。

namespace Example;
//饮料接口
interface Beverage
{
    //返回商品描述
    public function getDescription();
    //返回价格
    public function getPrice();
}
//CoffeeBean1——具体被装饰的对象类1
class CoffeeBean1 implements Beverage
{
    private  $description = "第一种咖啡豆";

    public function getDescription() {
        return $this->description;
    }

	public function getPrice() {
		return 50;
	}
}
//CoffeeBean2——具体被装饰的对象类2
class CoffeeBean2 implements Beverage
{
    private  $description = "第二种咖啡豆";

    public function getDescription() {
        return $this->description;
    }

    public function getPrice() {
        return 100;
    }
}
//Decorator——抽象装饰类
class Decorator implements Beverage
{
    private $description = "我只是装饰器，不知道具体的描述";

    public function getDescription() {
        return $this->description;
    }

	public function getPrice() {
		return 0;	//价格由子类来决定
	}
}
//Milk——具体装饰类，给咖啡加入牛奶
class Milk extends Decorator{
    private $description = "加了牛奶！";
    /** @var Beverage $beverage  */
    private $beverage = null;

    public function __construct(Beverage $beverage)
    {
        $this->$beverage = $beverage;
    }

    public function getDescription(){
		return $this->beverage->getDescription() . "\n" . $this->$description;
	}

	public function getPrice(){
		return $this->beverage->getPrice()+20;	//20表示牛奶的价格
	}
}
//Mocha——给咖啡加入摩卡
class Mocha extends Decorator{
    private $description = "加了摩卡！";
    /** @var Beverage $beverage  */
    private $beverage = null;

    public function __construct(Beverage $beverage)
    {
        $this->$beverage = $beverage;
    }

    public function getDescription(){
        return $this->beverage->getDescription() . "\n" . $this->$description;
    }

    public function getPrice(){
        return $this->beverage->getPrice()+30;	//30表示摩卡的价格
    }
}
//Soy——给咖啡加入豆浆
class Soy extends Decorator{
    private $description = "加了豆浆！";
    /** @var Beverage $beverage  */
    private $beverage = null;

    public function __construct(Beverage $beverage)
    {
        $this->$beverage = $beverage;
    }

    public function getDescription(){
        return $this->beverage->getDescription() . "\n" . $this->$description;
    }

    public function getPrice(){
        return $this->beverage->getPrice()+40;	//40表示豆浆的价格
    }
}
//客户端测试：
$beverage = new CoffeeBean1(); //选择了第一种咖啡豆磨制的咖啡
$beverage = new Mocha($beverage); //为咖啡加了摩卡
$beverage = new Milk($beverage);  //为咖啡加了牛奶
echo $beverage->getDescription() . '\n' . '加了摩卡和牛奶的咖啡价格：' . $beverage->getPrice();

