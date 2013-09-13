<?php
/**
 * 核心基础类
 * @see https://github.com/lay595715148/laywork3
 * 
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
use Exception;
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * <p>核心基础类</p>
 * <p>继承至此类的对象将会拥有setter和getter方法</p>
 * 
 * @abstract
 */
abstract class Base {
    /**
     * magic call method
     * @param string $method
     * @param mixed $arguments
     */
    public function __call($method, $arguments) {
        if(!method_exists($this,$method)) {
            throw new MethodNotFoundException('There is no object method:'.$method.'( ) in class:'.get_class($this));
        }
    }
    /**
     * magic static call method
     * @param string $method
     * @param mixed $arguments
     */
    public static function __callStatic($method, $arguments) {
        if(!method_exists($this,$method)) {
            throw new StaticMethodNotFoundException('There is no static method:'.$method.'( ) in class:'.get_class($this));
        }
    }
    /**
     * magic setter
     * 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value) {
        if(!property_exists($this,$name)) {
            throw new PropertyNotFoundException('There is no property:'.$name.' in class:'.get_class($this));
        }
    }
    /**
     * magic getter
     * 
     * @param string $name
     * @return void
     */
    public function __get($name) {
        if(!property_exists($this,$name)) {
            throw new PropertyNotFoundException('There is no property:'.$name.' in class:'.get_class($this));
        }
    }
}

/**
 * Property not found exception
 */
class PropertyNotFoundException extends Exception {}
/**
 * method not found exception
 */
class MethodNotFoundException extends Exception {}
/**
 * static method not found exception
 */
class StaticMethodNotFoundException extends Exception {}
?>