<?php
/**
 * 引语引擎基础类
 * @author Lay Li
 * @Version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
use cn\laysoft\laywork\demo\DemoPreface;
use Laywork,Debugger;
if(!defined('INIT_LAYWORK')) { exit; }

abstract class Preface extends Base {
    /**
     * @staticvar Preface instance
     */
    private static $instance = null;
    /**
     * get Preface instance 
     * @param $name name of Preface
     * @param $config default is empty
     * @return Preface
     */
    public static function newInstance($name = '', $config = '') {
        $config = is_array($config)?$config:Laywork::prefaceConfig($name);
        $classname = isset($config['classname'])?$config['classname']:'DemoPreface';
        Debugger::info("new preface($classname) instance", 'Preface');
        
        if(self::$instance == null) {
            if(isset($config['classname'])) {
                self::$instance = new $classname($config);
            } else {
                self::$instance = new DemoPreface($config);
            }
            if(!(self::$instance instanceof Preface)) {
                self::$instance = new DemoPreface($config);
            }
        }
        return self::$instance;
    }
    
    /**
     * 配置信息数组
     * @var array $config
     */
    protected $config = array();
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
    public function initialize() {//must return $this
        Debugger::info('initialize', 'Preface');
        return $this;
    }
}
?>