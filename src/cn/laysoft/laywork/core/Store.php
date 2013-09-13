<?php
/**
 * 数据库访问对象基础类
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
use cn\laysoft\laywork\demo\DemoStore;
use Laywork;
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * 数据库访问对象基础类
 * @abstract
 */
abstract class Store extends Base {
    /**
     * @staticvar store instance array
     */
    private static $instances = array();
    /**
     * get store instance 
     * @param $name name of store
     * @param $config default is empty
     * @return Store
     */
    public static function newInstance($name, $config = '') {
        $config = is_array($config)?$config:Laywork::storeConfig($name);
        $classname = isset($config['classname'])?$config['classname']:'DemoStore';
        
        if(!isset(self::$instances[$name])) {
            if(isset($config['classname'])) {
                self::$instances[$name] = new $classname($config);
            } else {
                self::$instances[$name] = new DemoStore($config);
            }
            if(!(self::$instances[$name] instanceof Store)) {
                self::$instances[$name] = new DemoStore($config);
            }
        }
        return self::$instances[$name];
    }
    
    /**
     * 配置信息数组
     * @var array
     */
    protected $config;
    /**
     * 构造方法
     * @param array $config 配置信息数组
     */
    protected function __construct($config = '') {
        $this->config = $config;
    }
    /**
     * 初始化
     */
    public function initialize() {
        return $this;
    }
}
?>