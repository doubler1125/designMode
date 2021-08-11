<?php
/**
 * 反射
 * PHP中的反射就像Java.lang.reflect包一样。它由一系列可以分析属性、方法和类的内置类组成。
 * 反射API的部分类：
 * Reflection           为类的摘要信息提供静态函数export()
 * ReflectionClass      类信息和工具
 * ReflectionMethod     类方法信息和工具
 * ReflectionParameter  方法参数信息
 * ReflectionProperty   类属性信息
 * ReflectionFunction   函数信息和工具
 * ReflectionExtension  PHP扩展信息
 * ReflectionException  错误类
 *
 * 由于反射API非常强大，应该经常使用反射API而少用类和对象函数。
 */

/**
 * ReflectionClass 检查类
 * 创建ReflectionClass对象后，就可以使用Reflection工具类输出类的相关信息。
 *
 * Reflection的静态方法export格式化和输出Reflection()对象管理的数据，包括属性和方法的访问控制状态，
 * 每个方法需要的参数以及每个方法在脚本文档中的位置。
 */
$prod_class = new ReflectionClass('CdProduct');
Reflection::export($prod_class);

$prod_class->getName();
$prod_class->isInternal(); //是否是内置类
$prod_class->isInterface();
$prod_class->isAbstract();
$prod_class->isFinal();
$prod_class->isInstantiable(); //是否可行可得到类的实例
//......

/**
 * ReflectionMethod 检查方法
 * 可以用于检查类中的方法
 */
$methods = $prod_class->getMethods();
$method = $prod_class->getMethod('指定的方法');
$method->getName();
$method->isInternal();
$method->isAbstract();
$method->isPublic();
$method->isProtected();
$method->isStatic();
$method->isConstructor();
//......

/**
 * ReflectionParameter 检查方法参数
 * 声明类方法时可以限制参数中对象的类型，因此检查方法的参数变得非常必要。
 * ReflectionParameter可以告诉参数的名称、类型、是否可以按饮用传递（前加&）、方法是否接受空值作为参数。
 */

$params = $method->getParameters();
foreach ($params as $param) {
    $param->getName();
    $param->getClass();
    $param->getPosition();
    $param->isPassedByReference(); //参数是否是引用
    $param->isDefaultValueAvailable();

}

//使用实例：假设我们要创建一个类来动态调用Module对象，
//          即该类可以自由加载第三方插件并继承进自己的系统，而不需要把第三方的代码硬编码进原有的代码。
//      可以在配置文件中列出所有的Module类，系统根据此来加载一定数据的Module对象，然后对每个对象调用execute()

class Person {
    public $name;
    function __construct($name) {
        $this->name = $name;
    }
}

interface Module {
    function execute();
}

class FtpModule implements Module
{
    function setHost($host) {
        print "FtpModule::setHost(): $host\n";
    }

    function setUser($user) {
        print "FtpModule::setUser(): $user\n";
    }

    function execute()
    {
        // TODO: Implement execute() method.
    }
}

class PersonModule implements Module
{
    function setPerson(Person $person) {
        print "PersonModule::setPerson(): {$person->name}\n";
    }

    function execute()
    {
        // TODO: Implement execute() method.
    }
}

// $module_class = new ReflectionClass($moudlename);
// $module = $module_class->newInstance();
class ModuleRunner {
    private $configData = [
        "PersonModule" => [
            'person' => 'bob',
        ],
        "FtpModule" => [
            'host' => 'example.com',
            'user' => 'anon',
        ]
    ];
    private $modules = [];

    function init() {
        $interface = new ReflectionClass('Module');
        foreach ($this->configData as $moudlename => $params) {
            $module_class = new ReflectionClass($moudlename);
            if (!$module_class->isSubclassOf($interface)) {
                throw new Exception("unknown module type: $moudlename");
            }
        }
        $module = $module_class->newInstance(); //实例化！！
        foreach ($module_class->getMethods() as $method) {
            $this->handleMethod($module, $method, $params); //对象、方法、参数
        }
        array_push($this->modules, $module);
    }

    function handleMethod(Module $module, ReflectionMethod $method, $params) {
        $name = $method->getName();
        $args = $method->getParameters();

        if (count($args) != 1 || substr($name, 0, 3) != "set") { //检查方法是否为有效的setter
            return false;
        }

        $property = strtolower(substr($name, 0, 3)); //检查$params数组是否包含某个属性
        if (!isset($params[$property])) {
            return false;
        }

        $arg_class = $args[0]->getClass();
        if (empty($arg_class)) {
            $method->invoke($module, $params[$property]); //$method方法，$module对象，参数类型为基本数据类型
        } else {
            $method->invoke($module, $arg_class->newInstance($params[$property])); //参数类型为对象
        }
        //反射方法: ReflectionMethod::invoke()
        //它以一个对象和任意数目的方法作为参数，如果提供的对象和方法不匹配会抛出异常
    }
}

$test = new ModuleRunner();
$test->init();

//在ModuleRunner::init()运行时，ModuleRunner对象存储着许多Module对象，而所有的Module对象都包含着数据。
//ModuleRunner类现在可以用一个类方法来循环遍历每个Module对象，并逐一调用各Module对象中的execute()方法。