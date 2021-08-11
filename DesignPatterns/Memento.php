<?php
namespace Memento;
class MementoPattern{};
/**
 * 备忘录模式（Memento）
 *
 * 含义：在不破坏封装性的前提下，捕获一个对象的内部状态，并在该对象之外保存这个状态。
 *      这样以后就可将该对象恢复到原先保存的状态。
 *
 * 组成：1、Originator（发起人），创建备忘录Memento并记录当前时刻内部状态、使用备忘录恢复内部状态。
 *      2、Memento（备忘录），存储Originator对象的内部状态。备忘录有两个接口，
 *                  Caretaker只能看到窄接口，只能将备忘录传递给其他对象。
 *                  Originator能够看到一个宽接口，允许它访问返回到先前状态所需的所有数据。
 *      3、Caretaker（管理者），负责保存好备忘录Memento，不能对备忘录的内容进行操作或检查。
 *
 * 优点：1、给用户提供了一种可以恢复状态的机制，可以使用户能够比较方便地回到某个历史的状态。
 *      2、实现了信息的封装，使得用户不需要关心状态的保存细节。
 * 缺点：消耗资源。如果类的成员变量过多，势必会占用比较大的资源，而且每一次保存都会消耗一定的内存。
 *
 * 适用场景：1、需要保存/恢复数据的相关状态场景。
 *         2、提供一个可回滚的操作。
 *
 * 应用举例：1、后悔药。 2、打游戏时的存档。 3、Windows 里的 ctri + z。 4、IE 中的后退。 4、数据库的事务管理。
 */

/**
 * Class Originator
 * @property string $state
 */
class Originator
{
    private $state = ''; //需要保存的属性，可能有多个

    public function createMemento() //创建备忘录
    {
        return (new Memento($this->state));
    }

    public function recoveryMemento(Memento $memento) //通过备忘录恢复
    {
        $this->state = $memento->getState();
    }

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

    public function show()
    {
        echo $this->state;
    }
}

/**
 * Class Memento
 * @package Memento
 */
class Memento
{
    private $state;

    public function __construct($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }
}

/**
 * Class Caretaker
 * @package Memento
 * @property string $memento
 */
class Caretaker
{
    private $memento = '';

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
}

$originator = new Originator();  //Originator初始状态为On
$originator->state = 'On';
$originator->show();

$caretaker = new Caretaker();
$caretaker->memento = $originator->createMemento();  //保存当前状态的备忘录
$originator->state = 'Off';
$originator->show();

$originator->recoveryMemento($caretaker->memento); //恢复某状态
$originator->show();
