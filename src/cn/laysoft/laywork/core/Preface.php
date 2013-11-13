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
    const TAG_PROVIDER = 'preface-provider';
    /**
     * @staticvar Preface instance
     */
    private static $instance = null;
    /**
     * get Preface instance 
     * @param string|array $name name or config of Preface
     * @param $config default is empty
     * @return Preface
     */
    public static function newInstance($name = '') {
        if(is_array($name)) {
            Debugger::info("new preface instance by config(json encoded):".json_encode($name), 'Preface');
        } else {
            Debugger::info("new preface instance by name:$name", 'Preface');
        }
        
        if(self::$instance == null) {//增加provider功能
            $provider = Laywork::get(self::TAG_PROVIDER);
            if($provider && is_string($provider)) {
                $provider = new $provider();
            }
            if($provider instanceof IPrefaceProvider) {
                self::$instance = $provider->provide($name);//执行provide方法
            }
            //如果没有自定义实现IPrefaceProvider接口的类对象，使用默认的配置项进行实现
            if(!(self::$instance instanceof Preface)) {
                $config = is_array($name)?$name:Laywork::prefaceConfig($name);
                $classname = isset($config['classname'])?$config['classname']:'DemoPreface';
                if(isset($config['classname'])) {
                    self::$instance = new $classname($config);
                }
                if(!(self::$instance instanceof Preface)) {
                    self::$instance = new DemoPreface($config);
                }
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