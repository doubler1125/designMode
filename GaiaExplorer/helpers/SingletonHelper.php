<?php

namespace Helpers;

trait SingletonHelper
{
    /** @var object[] */
    protected static $_instances;

    public static function instance($clz = null) {
        $clzReflection = new \ReflectionClass(get_called_class());
        if (isset(self::$_instances[$clz])) {
            return self::$_instances[$clz];
        }
        return $clzReflection->newInstanceArgs(func_get_args());
    }

//    /**
//     * disable clone
//     */
//    private function __clone()
//    {
//
//    }
//
//    /**
//     */
//    private function __wakeup()
//    {
//
//    }
}