<?php
/**
 * 业务逻辑处理对象基础类
 * @author Lay Li
 * @version 0.0.1 (bulid 130911)
 */
namespace cn\laysoft\laywork\core;
use cn\laysoft\laywork\demo\DemoService;
use Laywork,Debugger;
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * 业务逻辑处理对象基础类
 * @abstract
 */
abstract class Service extends Base {
    const TAG_PROVIDER = 'service-provider';
    /**
     * @staticvar service instance array
     */
    private static $instances = array();
    /**
     * get service instance 
     * @param string $name name or config of Service,default is empty
     * @param array $config config of Service,default is empty
     * @return Service
     */
    public static function getInstance($name = '', $config = '') {
        if(is_array($config) && !empty($config)) {
            Debugger::info("new service instance by name:$name and config(json encoded):".json_encode($config), 'Service');
        } else {
            Debugger::info("new service instance by name:$name", 'Service');
        }
        
        if(!isset(self::$instances[$name])) {//增加provider功能
            $provider = Laywork::get(self::TAG_PROVIDER);
            if($provider && is_string($provider)) {
                $provider = new $provider();
            }
            if($provider instanceof IServiceProvider) {
                self::$instances[$name] = $provider->provide($name);//执行provide方法
            } else if($provider) {
                Debugger::warn('given provider isnot an instance of IServiceProvider', 'SERVICE');
            }
            //如果没有自定义实现IServiceProvider接口的类对象，使用默认的配置项进行实现
            if(!isset(self::$instances[$name]) || !(self::$instances[$name] instanceof Service)) {
                $config = is_array($config)?$config:Laywork::serviceConfig($name);
                $classname = $config && isset($config['classname'])?$config['classname']:'DemoService';
                if(isset($config['classname'])) {
                    self::$instances[$name] = new $classname($config);
                }
                if(!isset(self::$instances[$name]) || !(self::$instances[$name] instanceof Service)) {
                    Debugger::warn('service has been instantiated by default DemoService', 'SERVICE');
                    self::$instances[$name] = new DemoService($config);
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
     * 一个Bean对象
     * @var Bean
     */
    public $bean;
    /**
     * 一个Store对象
     * @var Store
     */
    public $store;
    /**
     * 构造方法
     * @param array $config
     */
    public function __construct($config = '') {
        $this->config = $config;
    }
    /**
     * 初始化
     */
    public function initialize() {
        Debugger::info('initialize', 'SERVICE');
        $config = &$this->config;
        
        //加载配置中的bean
        if(is_array($config) && array_key_exists('bean', $config) && is_string($config['bean'])) {
            $this->bean = &Bean::getInstance($config['bean']);
        } else {
            $this->bean = &Bean::getInstance();
        }
        //加载配置中的store
        if(is_array($config) && array_key_exists('store', $config) && is_string($config['store'])) {
            $this->store = &Store::getInstance($config['store'], $this->bean);
            $this->store->initialize();
        } else {
            $this->store = &Store::getInstance('', $this->bean);
            $this->store->initialize();
        }
        Debugger::info("initialized", 'SERVICE');
    }
}
?>