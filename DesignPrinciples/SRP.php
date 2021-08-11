<?php
class SRP{};
/**
 * 单一职责原则（Single Responsibility Principle, SRP）
 *
 * 含义：应该有且仅有一个原因引起类的变更。
 *      一个 类/接口/方法 只负责一项职责或职能。
 *
 * 补充：如果一个类承担的职责过多，就等于把这些职责耦合在一起，一个职责的变化可能会削弱或者抑制这个类完成其他职责的能力。
 *      这种耦合会导致脆弱的设计，当变化发生时，设计会遭受到意想不到的破坏。
 *      软件设计真正要做的许多内容，就是发现职责并把那些职责相互分离。
 *
 * 优点：1、降低类的复杂度；
 *      2、提高类的可读性，因为类的职能单一，看起来比较有目的性，显得简单；
 *      3、提高系统的可维护性，降低变更程序引起的风险。
 *
 * 缺点：如果一味追求这个单一职责，有时也会造成类的大爆炸。。。不过接口和方法应该遵循这个原则。
 *
 */


//在软件开发中，经常会遇到一个功能类T负责两个不同的职责：职责P1，职责P2。
//现因需求变更需要更改职责P1来满足新的业务需求，当我们实现完成后，发现因更改职责P1竟导致原本能够正常运行的职责P2发生故障。
//而修复职责P2又不得不更改职责P1的逻辑，这便是因为功能类T的职责不够单一，职责P1与职责P2耦合在一起导致的。
//例如下面的工厂类，负责 将原料进行预处理然后加工成产品X和产品Y。
class Factory
{
    private function preProcess($material)
    {
        return "*" . $material . '*';
    }

    public function processX($material)
    {
        return $this->preProcess($material) . '加工成：产品X';
    }

    public function processY($material)
    {
        return $this->preProcess($material) . '加工成：产品Y';
    }
}
$factory = new Factory();
echo $factory->processX('原料');
echo $factory->processY('原料');
//现因市场需求，优化产品X的生产方案，需要改变原料预处理的方式将预处理方法preProcess的"*"改为"#"
//从运行结果中可以发现，在使产品X可以达到预期生产要求的同时，也导致了产品Y的变化，但是产品Y的变化并不在预期当中，这便导致程序运行错误甚至崩溃。
//为了避免这种问题的发生，我们在软件开发中，应当注重各职责间的解耦和增强功能类的内聚性，来实现类的职责的单一性。
//按照单一职责原则可以将上面的工厂类按照以下方式进行分解:
abstract class Afactory
{
    protected abstract function preProcess($material);
    protected abstract function process($material);
}

class FactoryX extends Afactory
{
    protected function preProcess($material)
    {
        return "*" . $material . '*';
    }
    public function process($material)
    {
        return $this->preProcess($material) . '加工成：产品X';
    }
}

class FactoryY extends Afactory
{
    protected function preProcess($material)
    {
        return "#" . $material . '#';
    }
    public function process($material)
    {
        return $this->preProcess($material) . '加工成：产品Y';
    }
}

$factory = new FactoryX();
echo $factory->process('原料');
$factory = new FactoryY();
echo $factory->process('原料');
//尽管我们在开发设计程序时，总想着要使类的职责单一，保证类的高内聚低耦合，但是很多耦合往往发生在不经意间，其原因为：类的职责扩散。
//由于软件的迭代升级，类的某一职责会被分化为颗粒度更细的多个职责。这种分化分为横向细分和纵向细分两种形式。
//例如下面的工厂类负责将原料多次加工处理后生产产品X:
class BFactory
{
    private function preProcess($material)
    {
        return "*" . $material . '--->';
    }

    private function process($material)
    {
        return $this->preProcess($material) . '加工——>';
    }

    public function packaging($material)
    {
        return $this->process($material) . '包装——>';
    }

    public function processX($material)
    {
        return $this->packaging($material) . '产品X';
    }

    public function processY($material)
    {
        return $this->packaging($material) . '产品Y';
    }
}
$factory = new BFactory();
echo $factory->processX('原料');
//横向细分
//现因业务拓展，工厂增加生产产品Y，产品Y与产品X除了包装不同之外，其它都一样，只需要在Bfactory中增加 processY 方法
$factory = new BFactory();
echo $factory->processX('原料');
echo $factory->processY('原料');
//纵向细分
//因业务拓展，工厂除了生产产品X，还生产半成品，简单包装一下就可以了，不需要贴上产品X的商标。需要把packaging变为共有的，然后直接调用
$factory = new BFactory();
echo $factory->processX('原料');
echo $factory->packaging('原料');

//这样之前的问题又出现了，如果优化产品X的生产方案，需要改变原料预处理的方式，同样也会牵连到其他生产过程的变化，无论是纵向的还是横向的。
//对于横向细分的情况，可以用复制大法将类Factory进行拆分，分成FactoryX和FactoryY两个类,纵向细分的情况也可以再拆出一个类
//这样虽然解决了因职责横向细分或纵向细分导致的牵一发动全身的问题，但是这样就使代码完全失去复用性了，而且FactoryX,FactoryY的职责真正单一了吗？
//单一职责要求我们在编写类，抽象类，接口时，要使其功能职责单一纯碎，将导致其变更的因素缩减到最少，
//按照这个原则对于工厂类Factory我们重新调整一下实现方案
//将四个职责抽取成以下四个接口:
interface IPreProcess
{
    function preProcess($material);
}
interface IProcess
{
    function process($material);
}
interface IPackaging
{
    function packaging($material);
}
interface IFactory
{
    function process($material);
}
//有四个职责类分别实现这四个接口
class PreProcess implements IPreProcess
{
    function preProcess($material)
    {
        return "*" . $material . '--->';
    }
}
class Process implements IProcess
{
    private $_preProcess;

    function __construct(IPreProcess $preProcess)
    {
        $this->_preProcess = $preProcess;
    }

    function process($material)
    {
        return $this->_preProcess->preProcess($material) + '加工-->';
    }
}
class Packaging implements IPackaging
{
    private $_process;

    function __construct(IProcess $process)
    {
        $this->_process = $process;
    }

    function packaging($material)
    {
        return $this->_process->process($material) . '包装-->';
    }
}
class IFactoryX implements IFactory
{
    private $_packaging;

    function __construct(IPackaging $packaging)
    {
        $this->_packaging = $packaging;
    }

    function process($material)
    {
        return $this->_packaging->packaging($material) . '产品X';
    }
}
$factory = new IFactoryX(new Packaging(new Process(new PreProcess())));
echo $factory->process('原料');
//可以增加一个IFactory的实现类IFactoryY
class IFactoryY implements IFactory
{
    private $_packaging;

    function __construct(IPackaging $packaging)
    {
        $this->_packaging = $packaging;
    }

    function process($material)
    {
        return $this->_packaging->packaging($material) . '产品Y';
    }
}
$factory = new IFactoryY(new Packaging(new Process(new PreProcess())));
echo $factory->process('原料');
//如果因市场需求，优化产品X的生产方案，改变原料预处理的方式
//可以增加一个原料预处理接口IPreProcess的实现类PreProcessA
class PreProcessA implements IPreProcess
{
    function preProcess($material)
    {
        return '#' . $material . '-->';
    }
}
$factory = new IFactoryX(new Packaging(new Process(new PreProcessA())));
echo $factory->process('原料');
//可以看到，在优化产品X的生产方案，改变原料预处理的方式的过程中，并没有改变产品Y的正常生产。
//如果想生产产品X的半成品的话，不需更改生产代码，只需在场景类中直接调用即可
$packagingX = new Packaging(new Process(new PreProcess()));
$packagingX->packaging('原料');

/**
 * 单一职责原则不是单单的将类的功能进行颗粒化拆分，拆分的越细越好，这样虽然可以保证类的功能职责的单一性，
 * 但是也会导致类的数量暴增，功能实现复杂，一个功能需要多个功能细分后的类来完成，会造成类的调用繁琐，类间关系交织混乱，后期维护困难。
 * 所以单一职责原则并不是要求类的功能拆分的越细越好，对类的功能细分需要有个度，细分到什么程度才是最合适呢，
 * 细分到在应对未来的拓展时，有且仅有一个因素导致其变更。
 */