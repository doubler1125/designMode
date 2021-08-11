<?php
namespace State;
class StatePattern{};
/**
 * 状态模式（State），状态模式是一种对象行为型模式
 *
 * 含义：当一个对象的内在状态改变时允许改变其行为，这个对象看起来像是改变了其类。
 *
 * 组成：Context：环境类，实际上就是拥有状态的对象，有时候可以充当状态管理器(State Manager)的角色，可以在环境类中对状态进行切换操作，
 *               这个切换也可以在具体状态类中切换。
 *      State：抽象状态类，定义一个接口，用以封装环境（Context）对象的一个特定的状态所对应的行为
 *      ConcreteState：具体状态类，每一个具体状态类都实现了环境（Context）的一个状态所对应的行为
 *
 * 优点：1、封装了转换规则。
 *      2、枚举可能的状态，在枚举状态之前需要确定状态种类。
 *      3、将所有与某个状态有关的行为放到一个类中，并且可以方便地增加新的状态，只需要改变对象状态即可改变对象的行为。
 *      *4、把状态转换逻辑转移到表示不同状态到一系列状态类中，允许状态转换逻辑与状态对象合成一体，而不是某一个巨大的条件语句块。
 *      5、可以让多个环境对象共享一个状态对象，从而减少系统中对象的个数
 * 缺点：1、状态模式的使用必然会增加系统类和对象的个数。
 *      2、状态模式的结构与实现都较为复杂，如果使用不当将导致程序结构和代码的混乱。
 *      3、状态模式对"开闭原则"的支持并不太好，对于可以切换状态的状态模式，增加新的状态类需要修改那些负责状态转换的源代码，
 *      否则无法切换到新增状态，而且修改某个状态类的行为也需修改对应类的源代码。
 *
 * 适用环境：1、行为随状态改变而改变的场景。
 *          2、条件、分支语句的代替者。
 */

class Context
{
    /** @var State */
    private $state;

    public function setState(State $state) {
        $this->state = $state;
    }

    //用户感兴趣的接口方法
    public function request($sampleParameter) {
        //转调state来处理
        $this->state->handle($sampleParameter);
    }
}

interface State
{
    public function handle($sampleParameter);
}

class ConcreteStateA implements State
{
    public function handle($sampleParameter)
    {
        print_r('ConcreteStateA handle ：' . $sampleParameter);
    }
}
class ConcreteStateB implements State
{
    public function handle($sampleParameter)
    {
        print_r('ConcreteStateB handle ：' . $sampleParameter);
    }
}

//创建状态
$state = new ConcreteStateA();
//创建环境
$context = new Context();
//将状态设置到环境中
$context->setState($state);
//请求
$context->request('test');

