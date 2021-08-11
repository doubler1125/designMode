<?php
namespace Command;
class CommandPattern{};
/**
 * 命令模式（Command）
 *
 * 含义：将一个请求封装为一个对象，从而使你可用不同的请求对客户进行参数化；
 *      对请求排队或记录请求日志，以及支持可撤销的操作。
 *
 * 组成：Command: 抽象命令类，用来声明执行操作的接口
 *      ConcreteCommand: 具体命令类，将一个接收者对象绑定于一个动作，调用接收者相应的操作，以实现Execute
 *      Invoker: 调用者，要求命令执行这个请求
 *      Receiver: 接收者，知道如何实施与执行一个请求相关的操作，任何类都可能作为一个接收者
 *
 * 优点：降低系统的耦合度。
 *      新的命令可以很容易地加入到系统中。
 *      可以比较容易地设计一个命令队列和宏命令（组合命令）。
 *      可以方便地实现对请求的Undo和Redo。
 * 缺点：使用命令模式可能会导致某些系统有过多的具体命令类。因为针对每一个命令都需要设计一个具体命令类，因此某些系统可能需要大量具体命令类，这将影响命令模式的使用。
 *
 * 适用环境：
 *      系统需要将请求调用者和请求接收者解耦，使得调用者和接收者不直接交互。
 *      系统需要在不同的时间指定请求、将请求排队和执行请求。
 *      系统需要支持命令的撤销(Undo)操作和恢复(Redo)操作。
 *      系统需要将一组操作组合在一起，即支持宏命令
 *
 * 拓展：宏命令又称为组合命令，它是命令模式和组合模式联用的产物。
 *      宏命令也是一个具体命令，不过它包含了对其他命令对象的引用，在调用宏命令的execute()方法时，将递归调用它所包含的每个成员命令的execute()方法，
 *      一个宏命令的成员对象可以是简单命令，还可以继续是宏命令。执行一个宏命令将执行多个具体命令，从而实现对命令的批处理。
 */

abstract class Command
{
    /** @var Receiver */
    protected $receiver;

    public function __construct($receiver)
    {
        $this->receiver = $receiver;
    }

    abstract function Execute();
}

class ConcreteCommand extends Command
{
    public function Execute()
    {
        $this->receiver->Action();
    }
}

class Invoker
{
    /** @var Command[]  */
    private $commandList = [];           //可以放多条命令，取消命令，最后顺序执行

    public function SetCommand(Command $command)
    {
        $this->commandList[] = $command;
    }

    public function ExecuteCommand()
    {
        foreach ($this->commandList as $command) {
            $command->Execute();
        }
    }
}

class Receiver
{
    public function Action()
    {
        echo '执行请求!';
    }
}

$receiver = new Receiver();
$command = new ConcreteCommand($receiver);
$invoker = new Invoker();
$invoker->SetCommand($command);
$invoker->ExecuteCommand();