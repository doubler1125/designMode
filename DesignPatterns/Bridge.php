<?php
namespace Bridge;
class BridgePattern{};
/**
 * 桥接模式（Bridge）
 *
 * 含义：将抽象部分与它的实现部分分离，使得它们都可以独立地变化。
 *      （在一个软件系统的抽象化和实现化之间使用关联关系（组合或者聚合关系）而不是继承关系）。
 * 理解桥接模式，重点需要理解如何将抽象化(Abstraction)与实现化(Implementation)脱耦，使得二者可以独立地变化。
 * 抽象化：抽象化就是忽略一些信息，把不同的实体当作同样的实体对待。在面向对象中，将对象的共同性质抽取出来形成类的过程即为抽象化的过程。
 * 实现化：针对抽象化给出的具体实现，就是实现化，抽象化与实现化是一对互逆的概念，实现化产生的对象比抽象化更具体，是对抽象化事物的进一步具体化的产物。
 * 脱耦：脱耦就是将抽象化和实现化之间的耦合解脱开，或者说是将它们之间的强关联改换成弱关联，将两个角色之间的继承关系改为关联关系。
 *
 * 补充：
 * 设想如果要绘制矩形、圆形、椭圆、正方形，我们至少需要4个形状类，但是如果绘制的图形需要具有不同的颜色，如红色、绿色、蓝色等，此时至少有如下两种设计方案：
 * 第一种设计方案是为每一种形状都提供一套各种颜色的版本。
 * 第二种设计方案是根据实际需要对形状和颜色进行组合
 * 对于有两个变化维度（即两个变化的原因）的系统，采用方案二来进行设计系统中类的个数更少，且系统扩展更为方便。设计方案二即是桥接模式的应用。
 * *桥接模式将继承关系转换为关联关系，从而降低了类与类之间的耦合，减少了代码编写量。
 *
 * 组成：1、Abstraction：抽象类
 *      2、RefinedAbstraction：扩充抽象类
 *      3、Implementor：实现类接口
 *      4、ConcreteImplementor：具体实现类
 *
 * 优点：分离抽象接口及其实现部分。
 *      桥接模式有时类似于多继承方案，但是多继承方案违背了类的单一职责原则（即一个类只有一个变化的原因），复用性比较差，而且多继承结构中类的个数非常庞大，桥接模式是比多继承方案更好的解决方法。
 *      桥接模式提高了系统的可扩充性，在两个变化维度中任意扩展一个维度，都不需要修改原有系统。
 *      实现细节对客户透明，可以对用户隐藏实现细节。
 * 缺点：桥接模式的引入会增加系统的理解与设计难度，由于聚合关联关系建立在抽象层，要求开发者针对抽象进行设计与编程。
 *
 * 适用场景：
 *      需要在构件的抽象化角色和具体化角色之间增加更多的灵活性，避免在两个层次之间建立静态的继承联系；
 *      抽象化角色和实现化角色可以以继承的方式独立扩展而互不影响；
 *      一个类存在两个独立变化的维度，且这两个维度都需要进行扩展；
 *      设计要求需要独立管理抽象化角色和具体化角色；
 *      不希望使用继承或因为多层次继承导致系统类的个数急剧增加的系统。
 *
 * 拓展：桥接模式与适配器模式
 *      桥接模式和适配器模式用于设计的不同阶段，桥接模式用于系统的初步设计，对于存在两个独立变化维度的类可以将其分为抽象化和实现化两个角色，使它们可以分别进行变化；
 *      而在初步设计完成之后，当发现系统与已有类无法协同工作时，可以采用适配器模式。但有时候在设计初期也需要考虑适配器模式，特别是那些涉及到大量第三方应用接口的情况。
 */

abstract class Implementor
{
    public abstract function Operation();
}

class ConcreteImplementorA extends Implementor
{
    public function Operation()
    {
        echo '具体实现A的方法执行';
    }
}
class ConcreteImplementorB extends Implementor
{
    public function Operation()
    {
        echo '具体实现B的方法执行';
    }
}

class Abstraction
{
    /** @var Implementor */
    protected $implementor;

    public function SetImplementor(Implementor $implementor)
    {
        $this->implementor = $implementor;
    }

    public function Operation()
    {
        $this->implementor->Operation();
    }
}

class RefinedAbstraction extends Abstraction
{
    public function Operation()
    {
        parent::Operation();
    }
}

$abstraction = new RefinedAbstraction();
$abstraction->SetImplementor(new ConcreteImplementorA());
$abstraction->Operation();
$abstraction->SetImplementor(new ConcreteImplementorB());
$abstraction->Operation();