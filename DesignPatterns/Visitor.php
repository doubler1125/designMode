<?php
namespace Visitor;
class VisitorPattern{};
/**
 * 访问者模式（Visitor）
 *
 * 含义：表示一个作用于某对象结构中的各元素的操作。它使你可以在不改变各元素的前提下定义作用于这些元素的操作。
 *
 * 组成：Visitor，抽象访问者，声明访问者可以访问哪些元素，具体到程序中就是visit方法中的参数定义哪些对象是可以被访问的。
 *      ConcreteVisitor，具体访问者，实现每个由Visitor声明的操作。
 *      Element，抽象元素，声明接受哪一类访问者访问，程序上是通过accept方法中的参数来定义的。抽象元素一般有两类方法，一部分是本身的业务逻辑，另外就是允许接收哪类访问者来访问。
 *      ConcreteElement，具体元素，实现Accept操作
 *      ObjectStructure，结构对象，一个具体元素的容器，可以提供一个高层的接口以允许访问者访问它的元素。

 *
 * 优点：增加新的操作很容易，因为增加新的操作就意味着增加一个新的访问者。访问者模式将有关的行为集中到一个访问者对象中。
 * 缺点：1、具体元素对访问者公布细节，违反了迪米特原则。 2、具体元素变更比较困难。 3、违反了依赖倒置原则，依赖了具体类，没有依赖抽象。
 *
 * 适用场景：1、对象结构中对象对应的类很少改变，但经常需要在此对象结构上定义新的操作。
 *         2、需要对一个对象结构中的对象进行很多不同的并且不相关的操作，而需要避免让这些操作"污染"这些对象的类，也不希望在增加新操作时修改这些类。
 *
 * 模式举例：见最下方代码
 */

//为该对象结构中ConcreteElement的每一个类声明一个Visit操作
abstract class Visitor
{
    abstract function VisitConcreteElementA(ConcreteElementA $concreteElementA);
    abstract function VisitConcreteElementB(ConcreteElementB $concreteElementB);
}

class ConcreteVisitor1 extends Visitor
{
    public function VisitConcreteElementA(ConcreteElementA $concreteElementA)
    {
        echo get_class($concreteElementA) . '被' . get_called_class() . '访问' . PHP_EOL;
    }
    public function VisitConcreteElementB(ConcreteElementB $concreteElementB)
    {
        echo get_class($concreteElementB) . '被' . get_called_class() . '访问' . PHP_EOL;
    }
}

class ConcreteVisitor2 extends Visitor
{
    public function VisitConcreteElementA(ConcreteElementA $concreteElementA)
    {
        echo get_class($concreteElementA) . '被' . get_called_class() . '访问' . PHP_EOL;
    }
    public function VisitConcreteElementB(ConcreteElementB $concreteElementB)
    {
        echo get_class($concreteElementB) . '被' . get_called_class() . '访问' . PHP_EOL;
    }
}

abstract class Element
{
    abstract function Accept(Visitor $visitor);
}

class ConcreteElementA extends Element
{
    public function Accept(Visitor $visitor)
    {
        $visitor->VisitConcreteElementA($this); //充分利用双分派技术，实现处理与数据结构的分离
    }
    public function OperationA() {
        //其他的相关方法
    }
}
class ConcreteElementB extends Element
{
    public function Accept(Visitor $visitor)
    {
        $visitor->VisitConcreteElementB($this); //充分利用双分派技术，实现处理与数据结构的分离
    }
    public function OperationB() {
        //其他的相关方法
    }
}

class ObjectStructure
{
    private $elements = [];

    public function Attach(Element $element)
    {
        $this->elements[] = $element;
    }
    public function Detach(Element $element)
    {
        foreach ($this->elements as $key => $item) {
            if ($item === $element) {
                unset($this->elements[$key]);
            }
        }
    }
    public function Accept(Visitor $visitor) {
        foreach ($this->elements as $element) {
            $element->Accept($visitor);
        }
    }
}

$objectStructure = new ObjectStructure();
$objectStructure->Attach(new ConcreteElementA());
$objectStructure->Attach(new ConcreteElementB());

$concreteVisitor1 = new ConcreteVisitor1();
$concreteVisitor2 = new ConcreteVisitor2();

$objectStructure->Accept($concreteVisitor1);
$objectStructure->Accept($concreteVisitor2);

/**
 * 如果老师教学反馈得分大于等于85分、学生成绩大于等于90分，则可以入选成绩优秀奖；
 * 如果老师论文数目大于8、学生论文数目大于2，则可以入选科研优秀奖。
 *
 * 在这个例子中，老师和学生就是Element，他们的数据结构稳定不变。
 * 从上面的描述中，我们发现，对数据结构的操作是多变的，一会儿评选成绩，一会儿评选科研，这样就适合使用访问者模式来分离数据结构和操作。
 *
序号	类名	                角色	            说明
1	Visitor	            Visitor	        抽象访问者
2	GradeSelection	    ConcreteVisitor	具体访问者
3	ResearcherSelection	ConcreteVisitor	具体访问者
4	Element	            Element	        抽象元素
5	Teacher	            ConcreteElement	具体元素
6	Student	            ConcreteElement	具体元素
7	ObjectStructure	    ObjectStructure	对象结构
*/

namespace VisitorExample;

//Visitor 抽象访问者
interface Visitor
{
    public function visitS(Student $element);
    public function visitT(Teacher $element);
}

//GradeSelection 选拔优秀成绩者
class GradeSelection implements Visitor
{
    private $awardWords = '[%s]分数是%d，荣获了成绩优秀奖。';

    public function visitS(Student $element) {
        // 如果学生考试成绩超过90，则入围成绩优秀奖。
        if ($element->getScore() >= 90) {
            echo sprintf($this->awardWords, $element->getName(), $element->getScore()) . PHP_EOL;
        }
    }
    public function visitT(Teacher $element) {
        // 如果老师反馈得分超过85，则入围成绩优秀奖。
        if ($element->getScore() >= 85) {
            echo sprintf($this->awardWords, $element->getName(), $element->getScore()) . PHP_EOL;
        }
    }
}

//ResearcherSelection，选拔优秀科研者
class ResearcherSelection implements Visitor
{
    private $awardWords = '[%s]的论文数是%d，荣获了科研优秀奖。';

    public function visitS(Student $element) {
        // 如果学生发表论文数超过2，则入围科研优秀奖。
        if ($element->getPaperCount() > 2) {
            echo sprintf($this->awardWords, $element->getName(), $element->getPaperCount()) . PHP_EOL;
        }
    }
    public function visitT(Teacher $element) {
        // 如果老师发表论文数超过8，则入围科研优秀奖。
        if ($element->getPaperCount() > 8) {
            echo sprintf($this->awardWords, $element->getName(), $element->getPaperCount()) . PHP_EOL;
        }
    }
}

//Element，抽象元素角色
interface Element
{
    public function accept(Visitor $visitor);
}

//Teacher，具体元素
class Teacher implements Element
{
    private $name;
    private $score;
    private $paperCount;

    public function __construct($name, $score, $paperCount)
    {
        $this->name = $name;
        $this->score = $score;
        $this->paperCount = $paperCount;
    }

    public function accept(Visitor $visitor)
    {
        $visitor->visitT($this);
    }

    public function getName() {
        return $this->name;
    }
    public function getScore() {
        return $this->score;
    }
    public function getPaperCount() {
        return $this->paperCount;
    }
}

//Student，具体元素
class Student implements Element
{
    private $name;
    private $score;
    private $paperCount;

    public function __construct($name, $score, $paperCount)
    {
        $this->name = $name;
        $this->score = $score;
        $this->paperCount = $paperCount;
    }

    public function accept(Visitor $visitor)
    {
        $visitor->visitS($this);
    }

    public function getName() {
        return $this->name;
    }
    public function getScore() {
        return $this->score;
    }
    public function getPaperCount() {
        return $this->paperCount;
    }
}

//ObjectStructure， 对象结构
class ObjectStructure
{
    private $elements = [];

    //访问者访问元素的入口
    public function accept(Visitor $visitor) {
        foreach ($this->elements as $element) {
            $element->accept($visitor);
        }
    }

    public function addElement(Element $element) {
        $this->elements[] = $element;
    }
    public function removeElement(Element $element) {
        foreach ($this->elements as $key => $e) {
            if ($e === $element) {
                unset($this->elements[$key]);
            }
        }
    }
}

// 初始化元素
$stu1 = new Student('Student Jim', 92, 3);
$stu2 = new Student('Student Ana', 89, 1);
$tea1 = new Teacher('Teacher Mike', 83, 10);
$tea2 = new Teacher('Teacher Lee', 88, 7);

// 初始化对象结构
$objectStructure = new ObjectStructure();
$objectStructure->addElement($stu1);
$objectStructure->addElement($stu2);
$objectStructure->addElement($tea1);
$objectStructure->addElement($tea2);

// 定义具体访问者，选拔成绩优秀者
$gradeSelection = new GradeSelection();
// 具体的访问操作，打印输出访问结果
$objectStructure->accept($gradeSelection);
echo PHP_EOL . '----结构不变，操作易变----';
// 数据结构是没有变化的，如果我们还想增加选拔科研优秀者的操作，那么如下。
$researcherSelection = new ResearcherSelection();
$objectStructure->accept($researcherSelection);


