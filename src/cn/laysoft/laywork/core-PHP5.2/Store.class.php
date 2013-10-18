<?php
/**
 * 数据库访问对象基础类
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
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
    public static function newInstance($name = '', $bean = null, $config = '') {
        $config = is_array($config)?$config:Laywork::storeConfig($name);
        $classname = $config && isset($config['classname'])?$config['classname']:'DemoStore';
        Debugger::info("new store($classname) instance", 'Store', __LINE__, __METHOD__, __CLASS__);
        
        if(!isset(self::$instances[$name])) {
            if(isset($config['classname'])) {
                self::$instances[$name] = new $classname($config, $bean);
            } else {
                self::$instances[$name] = new DemoStore($config, $bean);
            }
            if(!(self::$instances[$name] instanceof Store)) {
                self::$instances[$name] = new DemoStore($config, $bean);
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
     * @var TableBean 默认使用到的数据模型
     */
    protected $bean;
    /**
     * 构造方法
     * @param array $config 配置信息数组
     * @param Bean $config 配置信息数组
     */
    protected function __construct($config = '', $bean = null) {
        $this->config = $config;
        $this->bean = $bean;
    }
    /**
     * 初始化
     */
    public function initialize() {
        Debugger::info('initialize', 'Store', __LINE__, __METHOD__, __CLASS__);
        return $this;
    }
}
?>