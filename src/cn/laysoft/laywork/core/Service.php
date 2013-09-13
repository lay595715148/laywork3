<?php
namespace cn\laysoft\laywork\core;
use cn\laysoft\laywork\demo\DemoService;
use Laywork;
if(!defined('INIT_LAYWORK')) { exit; }

abstract class Service extends Base {
    private static $instances = array();
    public static function newInstance($name, $config = '') {
        $config = is_array($config)?$config:Laywork::serviceConfig($name);
        $classname = isset($config['classname'])?$config['classname']:'DemoService';
        
        if(!isset(self::$instances[$name])) {
            if(isset($config['classname'])) {
                self::$instances[$name] = new $classname($config);
            } else {
                self::$instances[$name] = new DemoService($config);
            }
            if(!(self::$instances[$name] instanceof Action)) {
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
    
    public function initialize() {
        
    }
}
?>