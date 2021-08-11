<?php
namespace Facade;
class FacadePattern{};
/**
 * 外观模式 (Facade Pattern) 外观模式又称为门面模式，它是一种对象结构型模式。
 *
 * 含义：外部与一个子系统的通信必须通过一个统一的外观对象进行，
 *      为子系统中的一组接口提供一个一致的界面，外观模式定义了一个高层接口，这个接口使得这一子系统更加容易使用。
 *
 * 组成：Facade: 外观角色
 *      SubSystem:子系统角色
 *
 * 补充：外观模式也是“迪米特法则”的体现，通过引入一个新的外观类可以降低原有系统的复杂度，同时降低客户类与子系统类的耦合度。
 *
 * 优点：对客户屏蔽子系统组件，减少了客户处理的对象数目并使得子系统使用起来更加容易，它实现了子系统与客户之间的松耦合关系，
 *      并降低了大型软件系统中的编译依赖性，
 *      简化了系统在不同平台之间的移植过程
 *
 * 缺点：不能很好地限制客户使用子系统类，而且在如果不引入抽象外观类，增加新的子系统可能需要修改外观类或客户端的源代码，违背了“开闭原则”。
 *
 * 适用场景：
 *      在设计初期阶段，应该要有意识的将不同的两个层分离(比如数据访问层和业务逻辑层、业务逻辑层和表示层)，层与层之间建立外观Facade；
 *      在开发阶段，增加外观Facade可以提供一个简单的接口，减少他们之间的依赖；
 *      在维护一个遗留的大型系统时，为系统开发一个外观Facade类，来提供设计粗糙或高度复杂的遗留代码的比较清晰简单的接口，让系统与Facade对象交互，Facade与遗留代码交互所有复杂的工作。
 */

/**
 * 比如说我们去医院就诊，医院有医生员工系统，有药品系统，有患者资料系统。但是我们只是在前台挂个号，就能在其他系统里都看到我们。外观系统就差不多这样。
 */

//如果没有挂号系统的话，我们就先要去医生系统通知一下医生，然后去患者系统取一下患者资料交给医生，再去药品系统登记一下，最后到药房领药。
//医院医生员工系统
class DoctorSystem{
    //通知就诊医生
    static public function getDoctor($name){
        echo __CLASS__.":".$name."医生，挂你号".PHP_EOL;
        return new Doctor($name);
    }
}
//医生类
class Doctor{
    public $name;
    public function __construct($name){
        $this->name = $name;
    }
    public function prescribe($data){
        echo __CLASS__.":"."开个处方给你".PHP_EOL;
        return "祖传秘方，药到必死";
    }
}
//患者系统
class SufferSystem {
    static function getData($suffer){
        $data = $suffer."资料";
        echo  __CLASS__.":".$suffer."的资料是这些".PHP_EOL ;
        return  $data;
    }
}
//医药系统
class MedicineSystem {
    static function register($prescribe){
        echo __CLASS__.":"."拿到处方：".$prescribe."------------通知药房发药了".PHP_EOL;
        Shop::setMedicine("砒霜5千克");
    }
}
//药房
class shop{
    static public $medicine;
    static function setMedicine($medicine){
        self::$medicine = $medicine;
    }
    static function getMedicine(){
        echo __CLASS__.":".self::$medicine.PHP_EOL;
    }
}

//如果没有挂号系统，我们就诊的第一步
//通知就诊医生
$doct = DoctorSystem::getDoctor("顾夕衣");
//患者系统拿病历资料
$data = SufferSystem::getData("患者");
//医生看病历资料，开处方
$prscirbe = $doct->prescribe($data);
//医药系统登记处方
MedicineSystem::register($prscirbe);
//药房拿药
Shop::getMedicine();

echo PHP_EOL."--------有了挂号系统以后--------".PHP_EOL;

//挂号系统
class Facade{
    static public function regist($suffer,$doct){
        $doct = DoctorSystem::getDoctor($doct);
        //患者系统拿病历资料
        $data = SufferSystem::getData($suffer);
        //医生看病历资料，开处方
        $prscirbe = $doct->prescribe($data);
        //医药系统登记处方
        MedicineSystem::register($prscirbe);
        //药房拿药
        Shop::getMedicine();
    }
}
//患者只需要挂一个号，其他的就让挂号系统去做吧。
Facade::regist("叶好龙","贾中一");
