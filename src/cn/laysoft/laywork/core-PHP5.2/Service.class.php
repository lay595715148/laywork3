<?php
/**
 * 业务逻辑处理对象基础类
 * @author Lay Li
 * @version 0.0.1 (bulid 130911)
 */
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * 业务逻辑处理对象基础类
 * @abstract
 */
abstract class Service extends Base {
    /**
     * @staticvar service instance array
     */
    private static $instances = array();
    /**
     * get service instance 
     * @param $name name of service
     * @param $config default is empty
     * @return Service
     */
    public static function newInstance($name = '', $config = '') {
        $config = is_array($config)?$config:Laywork::serviceConfig($name);
        $classname = $config && isset($config['classname'])?$config['classname']:'DemoService';
        Debugger::info("new service($classname) instance", 'Service', __LINE__, __METHOD__, __CLASS__);
        
        if(!isset(self::$instances[$name])) {
            if(isset($config['classname'])) {
                self::$instances[$name] = new $classname($config);
            } else {
                self::$instances[$name] = new DemoService($config);
            }
            if(!(self::$instances[$name] instanceof Service)) {
                self::$instances[$name] = new DemoService($config);
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
     * 多个Bean对象
     * @var array<Bean>
     */
    protected $beans = array();
    /**
     * 一个Bean对象
     * @var Bean
     */
    protected $bean;
    /**
     * 一个Store对象
     * @var Store
     */
    protected $store;
    /**
     * 构造方法
     * @param array $config
     */
    protected function __construct($config = '') {
        $this->config = $config;
    }
    /**
     * 获取以某一个Bean对象
     * @param string $name
     * @return Bean
     */
    protected function bean($name) {
        $beans = &$this->beans;
        if(array_key_exists($name, $beans)) {
            return $beans[$name];
        } else if(is_string($name)) {
            $beans[$name] = Bean::newInstance($name);
            return $beans[$name];
        } else {
            return $beans['demo'];
        }
    }
    /**
     * 初始化
     */
    public function initialize() {
        Debugger::info('initialize', 'Service', __LINE__, __METHOD__, __CLASS__);
        $config = &$this->config;
        
        //加载配置中的bean
        if(is_array($config) && array_key_exists('bean', $config) && is_string($config['bean'])) {
            $this->bean = Bean::newInstance($config['bean']);
        } else {
            $this->bean = Bean::newInstance();
        }
        //加载配置中的store
        if(is_array($config) && array_key_exists('store', $config) && is_string($config['store'])) {
            $this->store = Store::newInstance($config['store'], $this->bean);
            $this->store->initialize();
        } else {
            $this->store = Store::newInstance('', $this->bean);
            $this->store->initialize();
        }
    }
}
?>