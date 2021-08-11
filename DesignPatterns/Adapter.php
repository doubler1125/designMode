<?php
namespace Adapter;
class AdapterPattern{};
/**
 * 适配器模式（Adapter）
 *
 * 含义：将一个类的接口转换成客户希望的另外一个接口。使得原本由于接口不兼容而不能一起工作的那些类可以一起工作。
 *
 * 组成：Target：目标抽象类，定义客户要用的特定领域的接口
 *      Adapter：适配器类，可以调用另一个接口，作为一个转换器，对适配者和抽象目标类进行适配，它是适配器模式的核心
 *      Adaptee：适配者类，被适配的角色，它定义了一个已经存在的接口，这个接口需要适配
 *      Client：客户类，针对目标抽象类进行编程，调用在目标抽象类中定义的业务方法
 *
 * 补充：适配器模式有对象适配器和类适配器两种实现。
 *
 * 优点：将目标类和适配者类解耦，通过引入一个适配器类来重用现有的适配者类，而无须修改原有代码。
 *      增加了类的透明性和复用性，将具体的实现封装在适配者类中，对于客户端类来说是透明的，而且提高了适配者的复用性。
 *      灵活性和扩展性都非常好，通过使用配置文件，可以很方便地更换适配器，也可以在不修改原有代码的基础上增加新的适配器类，完全符合“开闭原则”。
 * 类适配器模式还具有如下优点：
 *      由于适配器类是适配者类的子类，因此可以在适配器类中置换一些适配者的方法，使得适配器的灵活性更强。
 * 对象适配器模式还具有如下优点：
 *      一个对象适配器可以把多个不同的适配者适配到同一个目标，也就是说，同一个适配器可以把适配者类和它的子类都适配到目标接口。
 *
 * 类适配器模式的缺点如下：
 *      对于Java、C#等不支持多重继承的语言，一次最多只能适配一个适配者类，而且目标抽象类只能为抽象类，不能为具体类，其使用有一定的局限性，不能将一个适配者类和它的子类都适配到目标接口。
 * 对象适配器模式的缺点如下：
 *      与类适配器模式相比，要想置换适配者类的方法就不容易。如果一定要置换掉适配者类的一个或多个方法，就只好先做一个适配者类的子类，将适配者类的方法置换掉，然后再把适配者类的子类当做真正的适配者进行适配，实现过程较为复杂。
 *
 * 适用场景：
 *      系统需要使用现有的类，而这些类的接口不符合系统的需要。
 *      想要建立一个可以重复使用的类，用于与一些彼此之间没有太大关联的一些类，包括一些可能在将来引进的类一起工作。
 *
 * 应用实例： 1、美国电器 110V，中国 220V，就要有一个适配器将 110V 转化为 220V。
 *          2、JAVA JDK 1.1 提供了 Enumeration 接口，而在 1.2 中提供了 Iterator 接口，想要使用 1.2 的 JDK，则要将以前系统的 Enumeration 接口转化为 Iterator 接口，这时就需要适配器模式。
 *          3、在 LINUX 上运行 WINDOWS 程序。 4、JAVA 中的 jdbc
 */

//例子：计算机读取TF卡（计算机本身可读取SD卡）

//计算机读取SD卡：
interface SDCard
{
    public function readSD();
    public function writeSD($msg = '');
}
class SDCardImpl implements SDCard
{
    public function readSD()
    {
        sprintf('读取SD卡');
    }

    public function writeSD($msg = '')
    {
        sprintf('写SD卡' . $msg);
    }
}
interface Computer
{
    public function readSD(SDCard $SDCard);
}
class ThinkpadComputer implements Computer
{
    public function readSD(SDCard $SDCard)
    {
        $SDCard->readSD();
    }
}
$computer = new ThinkpadComputer();
$sdCard = new SDCardImpl();
$computer->readSD($sdCard);

//通过适配器模式读取TF卡:
interface TFCard
{
    public function readTF();
    public function writeTF($msg = '');
}
class TFCardImpl implements TFCard
{
    public function readTF() {
        sprintf('读取TF卡');
    }
    public function writeTF($msg = '') {
        sprintf('写TF卡' . $msg);
    }
}
//创建SD适配TF的适配器
class SDAdapterTF implements SDCard //实现的SDCard
{
    /** @var $tfCard TFCard */
    private $tfCard;
    public function __construct(TFCard $tfCard)
    {
        $this->tfCard;
    }

    public function readSD()
    {
        $this->tfCard->readTF();
    }
    public function writeSD($msg = '')
    {
        $this->tfCard->writeTF($msg);
    }
}

$computer = new ThinkpadComputer();
$tfCard = new TFCardImpl();
$tfCardAdapterSD = new SDAdapterTF($tfCard);
$computer->readSD($tfCardAdapterSD);

