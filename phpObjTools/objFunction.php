<?php

/**
 * 查找类
 * class_exists() 类是否存在
 * get_declared_classes() 列出用户定义的类和PHP内置的类
 */

$classsname = 'Task';
$path = "task/{$classsname}.php";
if (!file_exists($path)) {
    throw new Exception('No such file');
}
require_once($path);

$qClassname = "tasks\\$classsname";
if (!class_exists($qClassname)) {
    throw new Exception('No such class');
}
$obj = new $qClassname;

print_r(get_declared_classes()); //get_declared_classes() 列出用户定义的类和PHP内置的类

/**
 * 了解对象或类
 * get_class() 返回类名
 * $obj instanceof $class 是否属于类
 * get_class_methods() 返回一个类中所有的方法的列表，入参为类名
 * get_class_vars() 查询类的属性
 * get_parent_class() 找一个类的父类
 */

if ($a instanceof $b) {}

if (method_exists($obj, $method)) {
    $obj->$method;
}

/**
 * 方法调用
 * call_user_func() call_user_func_array() 动态调用方法
 */

class example1
{
    private $obj = null;

    function __call($method, $args)
    {
        if (method_exists($this->obj, $method)) {
            return call_user_func_array([$this->obj, $method], $args);
        }
    }
}



