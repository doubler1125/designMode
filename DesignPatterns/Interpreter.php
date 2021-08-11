<?php
namespace Interpreter;
class InterpreterPattern{};
/**
 * 解释器模式（Interpreter）
 *
 * 含义：给定一个语言，定义它的文法的一种表示，并定义一个解释器，这个解释器使用该表示来解释语言中的句子。
 *
 * 组成：AbstractExpression，抽象表达式，表明一个抽象的解释操作，这个接口为抽象语法树中所有的节点所共享。
 *      TerminalExpression，终结符表达式，实现与文法中的终结符相关联的解释操作。
 *      NonterminalExpression，非终结符表达式，为文法中的非终结符实现解释操作。对文法中每一条规则R1、R2···Rn都需要一个具体的非终结符表达式类
 *      Context，包含解释器之外的一些全局信息。
 *
 * 优点：1、可扩展性比较好，灵活。 2、增加了新的解释表达式的方式。 3、易于实现简单文法。
 * 缺点：1、可利用场景比较少。 2、对于复杂的文法比较难维护。 3、解释器模式会引起类膨胀。 4、解释器模式采用递归调用方法。
 *
 * 适用场景：如果一种特定类型的问题发生的频率足够高，那么可能就值得将该问题的各个实例表述为一个简单语言中的句子。
 *         这样就可以构建一个解释器，该解释器通过解释这些句子来解决该问题。
 *
 * 应用举例：可利用场景比较少，JAVA 中如果碰到可以用 expression4J 代替。
 */

abstract class AbstractExpression
{
    abstract function Interpret(Context $context);
}

class TerminalExpression extends AbstractExpression
{
    public function Interpret(Context $context)
    {
        echo '终端解释器';
    }
}

class NonterminalExpression extends AbstractExpression
{
    public function Interpret(Context $context)
    {
        echo '非终端解释器';
    }
}

class Context
{
    private $input = '';
    private $output = '';

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }

    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        }
    }
}

$context = new Context();
$list = [];
$list[] = new TerminalExpression();
$list[] = new NonterminalExpression();
$list[] = new TerminalExpression();
$list[] = new TerminalExpression();

foreach ($list as $expression) {
    /** @var $expression AbstractExpression */
    $expression->Interpret($context);
}


/** 音乐解释器 */

//演奏内容
/**
 * Class PlayContext
 * @package Interpreter
 * @property $playText string
 */
class PlayContext
{
    private $playText = '';

    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }
    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        }
    }
}

//解释器
abstract class Expression
{
    public function Interpret(PlayContext $context) {
        if (strlen($context->playText) == 0) {
            return;
        }
        else {
            $playKey = substr($context->playText, 0, 1);
            $context->playText = substr($context->playText, 2);
            $playValue = intval(substr($context->playText, 0, strpos($context->playText, ' ')));
            $context->playText = substr($context->playText, strpos($context->playText, ' ') + 1);
            $this->excute($playKey, $playValue);
        }
    }

    public abstract function excute(string $key, int $value);
}

//音符类
class Note extends Expression
{
    public function excute(string $key, int $value)
    {
        $note = '';
        switch ($key) {
            case "C": $note = '1'; break;
            case "D": $note = '2'; break;
            case "E": $note = '3'; break;
            case "F": $note = '4'; break;
            case "G": $note = '5'; break;
            case "A": $note = '6'; break;
            case "B": $note = '7'; break;
        }
        echo $note;
    }
}

//音阶类
class Scale extends Expression
{
    public function excute(string $key, int $value)
    {
        $scale = '';
        switch ($value) {
            case 1: $scale = '低音'; break;
            case 2: $scale = '中音'; break;
            case 3: $scale = '高音'; break;
        }
        echo $scale;
    }
}

//客户端
$context = new PlayContext();
$context->playText = 'O 2 E 0.5 G 0.5 A 3 E 0.5 G 0.5 D 3 E 0.5 G 0.5 A 0.5 O 3 C 1 O 2 A 0.5'; //首字母是O表示后面的值是音阶 其他是音符

$expression = '';
try {
    while (strlen($context->playText) > 0) {
        $str = substr($context->playText, 0, 1);
        switch ($str) {
            case "O": $expression = new Scale(); break;  //这里应该用简单工厂和反射
            case "C":
            case "D":
            case "E":
            case "F":
            case "G":
            case "A":
            case "B":
            case "P":
                $expression = new Note(); break;
        }
        $expression->Interpret($context);
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}
