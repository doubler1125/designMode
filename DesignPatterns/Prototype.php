<?php
namespace Prototype;
class PrototypePattern{};
/**
 * 原型模式（Prototype）
 *
 * 含义：用原型实例指定创建对象的种类，并且通过拷贝这些原型创建新的对象。
 *
 * 补充：原型模式其实就是从一个对象再创建另外一个可定制的对象，而且不需要知道任何创建的细节。
 *      浅复制：
 *          被复制对象的所有变量都含有与原来的对象相同的值，而所有的对其他对象的引用都仍指向原来的对象(改一个造成全都改了)。
 *      深复制：
 *          把引用对象的变量指向复制过的新对象，而不是原有的被引用的对象（深入多少层及循环引用需要注意）。
 *
 * 优点：不用重新初始化对象，而是动态地获得对象运行时的状态。
 *
 * 适用场景：平行继承层次的出现是工厂方法模式带来的一个问题。每次添加产品家族时，被迫去创建一个相关的具体创建者。
 *      在一个快速增长的系统里包含越来越多的产品，而维护这种关系会很快令人厌烦。
 *      一个避免这种依赖的办法是适用PHP的clone关键词复制已存在的具体产品。然后，具体产品类本身便成为他们自己生成的基础。这便是原型模式。
 */

//假设有一款'文明'风格的游戏，可在区块组成的格子中操作战斗单元。每个区块分别代表海洋、平原和森林。

class Sea {};
class EarthSea extends Sea {};
class MarsSea extends Sea {}; //月球海

class Plains {}; //平原
class EarthPlains extends Plains {};
class MarsPlains extends Plains {};

class Forest {};
class EarthForest extends Forest {};
class MarsForest extends Forest {};

//地形工厂，用户可在不同的环境Earth、Mars里选择
class TerrainFactory
{
    private $sea;
    private $forest;
    private $plains;

    function __construct(Sea $sea, Plains $plains, Forest $forest) {
        $this->sea = $sea;
        $this->plains = $plains;
        $this->forest = $forest;
    }

    function getSea() {
        return clone $this->sea;  //浅复制！注意每次返回的都是一个新的副本
    }

    function getPlains() {
        return clone $this->plains;
    }

    function getForest() {
        return clone $this->forest;
    }
}

//当客户端代码调用getSea()时，返回在初始化时缓存的Sea对象的一个副本
$factory = new TerrainFactory(new EarthSea(), new EarthPlains(), new EarthForest());
print_r($factory->getSea());
print_r($factory->getPlains());
print_r($factory->getForest());
//现在在一个有类似地球海洋和森林并有火星平原的星球。原型模式使我们可利用组合所提供的灵活性。
$factory = new TerrainFactory(new EarthSea(), new MarsPlains(), new EarthForest());
//因为在运行时保存和克隆对象，所以当生成新产品时，可以重新设定对象状态。
class Sea2 { //即前面的Sea
    private $navigability = 0;
    function __construct($navigability) {
       $this->navigability = $navigability;
    }
}
$factory = new TerrainFactory(new EarthSea(-1), new MarsPlains(), new EarthForest());

//注意如果产品对象引用了其他对象，应该实现__clone()方法来保证得到的是深复制。
class Contained {};
class Container {
    public $contained;
    function __construct() {
        $this->contained = new Contained();
    }

    function __clone() {
        // 确保被克隆的对象持有的是self::$container的克隆而不是引用
        $this->contained = clone $this->contained;
    }
}