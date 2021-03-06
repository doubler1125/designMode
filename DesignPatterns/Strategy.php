<?php
namespace Strategy;
class StrategyPattern{};
/**
 * 策略模式
 *
 * 定义：策略模式定义了一系列的算法，并将每一个算法封装起来，使每个算法可以相互替代，使算法本身和使用算法的客户端分割开来，相互独立。
 *
 * 组成：1、策略接口角色IStrategy:用来约束一系列具体的策略算法，策略上下文角色ConcreteStrategy使用此策略接口来调用具体的策略所实现的算法。
 *　    2、具体策略实现角色ConcreteStrategy:具体的策略实现，即具体的算法实现。
 *　    3、策略上下文角色StrategyContext:策略上下文，负责和具体的策略实现交互，通常策略上下文对象会持有一个真正的策略实现对象，
 *      策略上下文还可以让具体的策略实现从其中获取相关数据，回调策略上下文对象的方法。
 *
 * 优点：1.策略模式的功能就是通过抽象、封装来定义一系列的算法，使得这些算法可以相互替换，所以为这些算法定义一个公共的接口，以约束这些算法的功能实现。如果这些算法具有公共的功能，可以将接口变为抽象类，将公共功能放到抽象父类里面。
 *      2.策略模式的一系列算法是可以相互替换的、是平等的，写在一起就是if-else组织结构，如果算法实现里又有条件语句，就构成了多重条件语句，可以用策略模式，避免这样的多重条件语句。
 *　    3.扩展性更好：在策略模式中扩展策略实现非常的容易，只要新增一个策略实现类，然后在使用策略实现的地方，使用这个新的策略实现就好了。
 *
 * 缺点：1.客户端必须了解所有的策略，清楚它们的不同：
 *　　　　如果由客户端来决定使用何种算法，那客户端必须知道所有的策略，清楚各个策略的功能和不同，这样才能做出正确的选择，但是这暴露了策略的具体实现。
 *　    2.增加了对象的数量：
 *　　　　由于策略模式将每个具体的算法都单独封装为一个策略类，如果可选的策略有很多的话，那对象的数量也会很多。
 *　　  3.只适合偏平的算法结构：
 *　　　　由于策略模式的各个策略实现是平等的关系（可相互替换），实际上就构成了一个扁平的算法结构。即一个策略接口下面有多个平等的策略实现（多个策略实现是兄弟关系），并且运行时只能有一个算法被使用。这就限制了算法的使用层级，且不能被嵌套。
 * 策略模式的本质：
　　分离算法，选择实现。
　　如果你仔细思考策略模式的结构和功能的话，就会发现：如果没有上下文，策略模式就回到了最基本的接口和实现了，只要是面向接口编程，就能够享受到面向接口编程带来的好处，通过一个统一的策略接口来封装和分离各个具体的策略实现，无需关系具体的策略实现。
　　貌似没有上下文什么事，但是如果没有上下文的话，客户端就必须直接和具体的策略实现进行交互了，尤其是需要提供一些公共功能或者是存储一些状态的时候，会大大增加客户端使用的难度；引入上下文之后，这部分工作可以由上下文来完成，客户端只需要和上下文进行交互就可以了。这样可以让策略模式更具有整体性，客户端也更加的简单。
　　策略模式体现了开闭原则：策略模式把一系列的可变算法进行封装，从而定义了良好的程序结构，在出现新的算法的时候，可以很容易的将新的算法实现加入到已有的系统中，而已有的实现不需要修改。
　　策略模式体现了里氏替换原则：策略模式是一个扁平的结构，各个策略实现都是兄弟关系，实现了同一个接口或者继承了同一个抽象类。这样只要使用策略的客户端保持面向抽象编程，就可以动态的切换不同的策略实现以进行替换。
 */

//策略代码的一般通用实现:

//策略接口
interface IStrategy
{
    //定义的抽象算法方法 来约束具体的算法实现方法
    public function algorithmMethod();
}

//具体的策略实现
class ConcreteStrategy implements IStrategy
{
    public function algorithmMethod()
    {
        echo "ConcreteStrategy method ...";
    }
}
class ConcreteStrategy2 implements IStrategy
{
    public function algorithmMethod()
    {
        echo "ConcreteStrategy2 method ...";
    }
}

//策略的上下文
class StrategyContext
{
    /** @var IStrategy $_strategy */
    private $_strategy;

    //使用构造器注入具体的策略类
    public function __construct(IStrategy $strategy)
    {
        $this->_strategy = $strategy;
    }

    //调用策略实现的方法
    public function contextMethod()
    {
        $this->_strategy->algorithmMethod();
    }
}

//外部客户端
//1.创建具体测策略实现
$strategy = new ConcreteStrategy2();
//2.在创建策略上下文的同时，将具体的策略实现对象注入到策略上下文当中
$ctx = new StrategyContext($strategy);
//3.调用上下文对象的方法来完成对具体策略实现的回调
echo $ctx->contextMethod();