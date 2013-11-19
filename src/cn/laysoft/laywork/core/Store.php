<?php
/**
 * 数据库访问对象基础类
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
use cn\laysoft\laywork\demo\DemoStore;
use Laywork,Debugger;
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * 数据库访问对象基础类
 * @abstract
 */
abstract class Store extends Base {
    const TAG_PROVIDER = 'store-provider';
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
        if(is_array($config) && !empty($config)) {
            Debugger::info("new store instance by name:$name and config(json encoded):".json_encode($config), 'STORE');
        } else {
            Debugger::info("new store instance by name:$name", 'STORE');
        }
        
        if(!isset(self::$instances[$name])) {//增加provider功能
            $provider = Laywork::get(self::TAG_PROVIDER);
            if($provider && is_string($provider)) {
                $provider = new $provider();
            }
            if($provider instanceof IStoreProvider) {
                self::$instances[$name] = $provider->provide($name);//执行provide方法
            }
            //如果没有自定义实现IStoreProvider接口的类对象，使用默认的配置项进行实现
            if(!isset(self::$instances[$name]) || !(self::$instances[$name] instanceof Store)) {
                $config = is_array($config)?$config:Laywork::storeConfig($name);
                $classname = $config && isset($config['classname'])?$config['classname']:'Mysql';
                if(isset($config['classname'])) {
                    self::$instances[$name] = new $classname($config, $bean);
                }
                if(!isset(self::$instances[$name]) || !(self::$instances[$name] instanceof Store)) {
                    Debugger::warn('store has been instantiated by default Mysql', 'STORE');
                    self::$instances[$name] = new Mysql($config, $bean);
                }
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
    public function __construct($config = '', $bean = null) {
        $this->config = $config;
        $this->bean = $bean;
    }
    /**
     * 初始化
     */
    public function initialize() {
        Debugger::info('initialize', 'STORE');
        return $this;
    }
}
?>