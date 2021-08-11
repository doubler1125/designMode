<?php
namespace Singleton;
class SingletonPattern{};
/**
 * 单例模式（Singleton）
 *
 * 含义：确保某一个类只有一个实例，而且自行实例化并向整个系统提供这个实例，这个类称为单例类，它提供全局访问的方法。
 *
 * 单例模式三要点：
 *      1、一是某个类只能有一个实例；
 *      2、二是它必须自行创建这个实例；
 *      3、三是它必须自行向整个系统提供这个实例。
 *
 * 组成：Singleton 单例类：构造函数为私有；提供一个自身的静态私有成员变量；提供一个公有的静态工厂方法。
 *
 * 优点：1、提供了对唯一实例的受控访问。因为单例类封装了它的唯一实例，所以它可以严格控制客户怎样以及何时访问它，并为设计及开发团队提供了共享的概念。
 *      2、由于在系统内存中只存在一个对象，因此可以节约系统资源，对于一些需要频繁创建和销毁的对象，单例模式无疑可以提高系统的性能。
 *      3、允许可变数目的实例。我们可以基于单例模式进行扩展，使用与单例控制相似的方法来获得指定个数的对象实例。
 * 缺点：1、由于单例模式中没有抽象层，因此单例类的扩展有很大的困难。
 *      2、单例类的职责过重，在一定程度上违背了“单一职责原则”。因为单例类既充当了工厂角色，提供了工厂方法，同时又充当了产品角色，包含一些业务方法，将产品的创建和产品的本身的功能融合到一起。
 *      3、滥用单例将带来一些负面问题，如为了节省资源将数据库连接池对象设计为单例类，可能会导致共享连接池对象的程序过多而出现连接池溢出；
 *      现在很多面向对象语言(如Java、C#)的运行环境都提供了自动垃圾回收的技术，因此，如果实例化的对象长时间不被利用，系统会认为它是垃圾，会自动销毁并回收资源，下次利用时又将重新实例化，这将导致对象状态的丢失。
 *
 * 适用环境：1、系统只需要一个实例对象，如系统要求提供一个唯一的序列号生成器，或者需要考虑资源消耗太大而只允许创建一个对象。
 *      2、客户调用类的单个实例只允许使用一个公共访问点，除了该公共访问点，不能通过其他途径访问该实例。
 *      3、在一个系统中要求一个类只有一个实例时才应当使用单例模式。反过来，如果一个类可以有几个实例共存，就需要对单例模式进行改进，使之成为多例模式。
 *
 * 应用举例：1、要求生产唯一序列号。
 *      2、WEB 中的计数器，不用每次刷新都在数据库里加一次，用单例先缓存起来。
 *      3、创建的一个对象需要消耗的资源过多，比如 I/O 与数据库的连接等。
 */

/**
 * 单例模式的几种实现方式：
 * 1、懒汉式，线程不安全
 * 是否 Lazy 初始化：是
 * 是否多线程安全：否
 * 这种方式是最基本的实现方式，这种实现最大的问题就是不支持多线程。如下面的PHP SingletonLazy代码，不过PHP是单进程或者并发，不是并行
 *
 * 2、懒汉式，线程安全
 * 是否 Lazy 初始化：是
 * 是否多线程安全：是
 * 这种方式具备很好的 lazy loading，能够在多线程中很好的工作，但是，必须加锁 synchronized 才能保证单例，但加锁会影响效率，99% 情况下不需要同步。
 *
 * 3、饿汉式
 * 是否 Lazy 初始化：否
 * 是否多线程安全：是
 * 方式比较常用，但容易产生垃圾对象。没有加锁，执行效率会提高。类加载时就初始化，浪费内存。如下SingletonNotLazy
 *
 * 4、双检锁/双重校验锁（DCL，即 double-checked locking）
 * 是否 Lazy 初始化：是
 * 是否多线程安全：是
 * 这种方式采用双锁机制，安全且在多线程情况下能保持高性能。
 *
 * 5、登记式/静态内部类
 * 是否 Lazy 初始化：是
 * 是否多线程安全：是
 * 这种方式能达到双检锁方式一样的功效，但实现更简单。对静态域使用延迟初始化，应使用这种方式而不是双检锁方式。这种方式只适用于静态域的情况，双检锁方式可在实例域需要延迟初始化时使用。
 *
 * 6、枚举
 * 是否 Lazy 初始化：否
 * 是否多线程安全：是
 * Java JDK1.5 起 这种实现方式还没有被广泛采用，但这是实现单例模式的最佳方法。它更简洁，自动支持序列化机制，绝对防止多次实例化。
 */

class SingletonLazy
{
    /** 静态成品变量 保存全局实例  */
    private static $_instance = null;

    /** 私有化默认构造方法，保证外界无法直接实例化 */
    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new SingletonLazy(); //或self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __clone()
    {
    }

    public function test()
    {
        echo '测试克隆';
    }
}

$instance = SingletonLazy::getInstance();
$instance->test();

class SingletonNotLazy {
    private static $_instance = self::class;
    private function __construct ()
    {
    }

    public static function getInstance() {
        return self::$_instance;
    }
}

