<?php
namespace Composite;
class CompositePattern{};
/**
 * 组合模式（Composite）
 *
 * 含义：将对象组合成树状结构以表示'部分-整体'的层次结构。
 *      组合模式是的用户对单个对象和组合对象的使用具有一致性。
 *
 * 组成：1、Component ：组合中的对象声明接口，在适当的情况下，实现所有类共有接口的默认行为。声明一个接口用于访问和管理Component子部件。
 *      2、Leaf：叶子对象。叶子结点没有子结点
 *      3、Composite：非叶子节点对象、容器对象，定义有枝节点行为，用来存储子部件，在Component接口中实现与子部件有关操作，如增加(add)和删除(remove)等。
 * 补充：叶子节点和容器对象都实现Component接口，这也是能够将叶子对象和容器对象一致对待的关键所在。
 *
 * 优点：1、可以清楚地定义分层次的复杂对象，表示对象的全部或部分层次，使得增加新构件也更容易。
 *      2、客户端调用简单，客户端可以一致的使用组合结构或其中单个对象。
 *      3、定义了包含叶子对象和容器对象的类层次结构，叶子对象可以被组合成更复杂的容器对象，而这个容器对象又可以被组合，这样不断递归下去，可以形成复杂的树形结构。
 *      4、更容易在组合体内加入对象构件，客户端不必因为加入了新的对象构件而更改原有代码。
 * 缺点：使设计变得更加抽象，对象的业务规则如果很复杂，则实现组合模式具有很大挑战性，而且不是所有的方法都与叶子对象子类都有关联
 *
 * 适用场景：1、您想表示对象的部分-整体层次结构（树形结构）。
 *         2、您希望用户忽略组合对象与单个对象的不同，用户将统一地使用组合结构中的所有对象。
 * 应用举例：1、算术表达式包括操作数、操作符和另一个操作数，其中，另一个操作符也可以是操作数、操作符和另一个操作数。
 *         2、把基本控件组合成定制的控件：用两个文本框和一个按钮组合成登录控件。
 */

abstract class Component
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public abstract function Add(Component $c); //通常都用Add和Remove方法来提供增加和移除树叶或树枝的功能
    public abstract function Remove(Component $c);

    public abstract function Display(int $depth);
}

class Leaf extends Component
{
    public function __construct($name)
    {
        parent::__construct($name);
    }

    public function Add(Component $c) {
        echo '叶子节点不能再增加分枝和树叶';
    }
    public function Remove(Component $c)
    {
        echo '叶子节点不能再删除分枝和树叶';
    }

    public function Display(int $depth) //叶子节点的具体方法
    {
        echo 'depth:' . $depth . str_pad('', $depth, '-') . ' name:' . $this->name . PHP_EOL;
    }
}

class Composite extends Component
{
    /** @var Component[] */
    private $children = []; //一个对象集合用来存储其下属的枝节点和叶节点

    public function __construct($name)
    {
        parent::__construct($name);
    }

    public function Add(Component $c)
    {
        $this->children[] = $c;
    }

    public function Remove(Component $c)
    {
        foreach ($this->children as $key => $child) {
            if ($child === $c) {
                unset($this->children[$key]);
            }
        }
    }

    public function Display(int $depth) //显示其枝节点名称，并对其下级进行遍历
    {
        echo 'depth:' . $depth . str_pad('', $depth, '-') . ' name:' . $this->name . PHP_EOL;
        foreach ($this->children as $child) {
            $child->Display($depth + 2);
        }
    }
}

$root = new Composite('root'); //生成树根root,根上长出两叶LeafA和LeafB
$root->Add(new Leaf("Leaf A"));
$root->Add(new Leaf("Leaf B"));

$comp = new Composite("Composite X");
$comp->Add(new Leaf("Leaf XA"));
$comp->Add(new Leaf("Leaf XB"));
$root->Add($comp); //根上长出分枝Composite X，分枝上也有两叶LeafXA和LeafXB

$comp2 = new Composite("Composite XY");
$comp2->Add(new Leaf("Leaf XYA"));
$comp2->Add(new Leaf("Leaf XYB"));
$comp->Add($comp2); //在Composite X 上再长出分枝Composite XY，分支上也有两叶LeafXYA和LeafXYB

$root->Add(new Leaf("Leaf C"));
$leaf = new Leaf("Leaf D");
$root->Add($leaf);
$root->Remove($leaf); //根节点加Leaf C, Leaf D，删Leaf D

$root->Display(1);
//运行结果：
//depth:1- name:root
//depth:3--- name:Leaf A
//depth:3--- name:Leaf B
//depth:3--- name:Composite X
//depth:5----- name:Leaf XA
//depth:5----- name:Leaf XB
//depth:5----- name:Composite XY
//depth:7------- name:Leaf XYA
//depth:7------- name:Leaf XYB
//depth:3--- name:Leaf C
