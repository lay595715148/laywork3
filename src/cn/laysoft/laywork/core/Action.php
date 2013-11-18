<?php

/**
 * 核心基础控制器
 * @see https://github.com/lay595715148/laywork3
 * 
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;

use cn\laysoft\laywork\demo\DemoAction;
use Laywork, Debugger;

if(! defined('INIT_LAYWORK')) {
    exit();
}

/**
 * <p>基础控制器</p>
 * <p>核心类，继承至此类的对象将会在运行时自动执行初始化init方法</p>
 *
 * @abstract
 *
 *
 */
abstract class Action extends Base {
    const DISPATCH_KEY = 'a';
    const DISPATCH_STYLE = '*';
    const TAG_PROVIDER = 'action-provider';
    /**
     *
     * @staticvar action instance
     */
    private static $instance = null;
    /**
     * get action instance
     *
     * @param string|array $name
     *            name or config of Action
     * @return Action
     */
    public static function newInstance($name = '') {
        if(is_array($name)) {
            Debugger::info("new action instance by config(json encoded):" . json_encode($name), 'Action');
        } else {
            Debugger::info("new action instance by name:$name", 'Action');
        }
        
        if(self::$instance == null) { // 增加provider功能
            $provider = Laywork::get(self::TAG_PROVIDER);
            if($provider && is_string($provider)) {
                $provider = new $provider();
            }
            if($provider instanceof IActionProvider) {
                self::$instance = $provider->provide($name); // 执行provide方法
            }
            // 如果没有自定义实现IActionProvider接口的类对象，使用默认的配置项进行实现
            if(! (self::$instance instanceof Action)) {
                $config = is_array($name) ? $name : Laywork::actionConfig($name);
                $classname = $config && isset($config['classname']) ? $config['classname'] : 'DemoAction';
                if(isset($config['classname'])) {
                    self::$instance = new $classname($config);
                }
                if(! (self::$instance instanceof Action)) {
                    self::$instance = new DemoAction($config);
                }
            }
        }
        return self::$instance;
    }
    
    /**
     *
     * @var array 配置信息数组
     */
    protected $config = array();
    /**
     *
     * @var array 存放配置的Service对象
     */
    protected $services = array();
    /**
     *
     * @var array 存放自注入的Bean对象
     */
    // protected $beans = array();
    /**
     *
     * @var Template 模板引擎对象
     */
    protected $template;
    /**
     *
     * @var Preface 引语引擎对象
     */
    protected $preface;
    /**
     * 构造方法
     *
     * @param array $config            
     */
    public function __construct($config = '') {
        $this->config = $config;
    }
    /**
     * 初始化
     *
     * @return Action
     */
    public function initialize() { // must return $this
        Debugger::info("initialize", 'Action');
        $config = &$this->config;
        $services = &$this->services;
        $template = &$this->template;
        $preface = &$this->preface;
        
        // 加载配置中的所有preface
        if(is_array($config) && array_key_exists('preface', $config)) {
            $preface = Preface::newInstance($config['preface']);
            $preface->initialize();
        } else {
            $preface = Preface::newInstance();
            $preface->initialize();
        }
        
        // 加载配置中的所有template
        if(is_array($config) && array_key_exists('template', $config)) {
            $template = Template::newInstance($config['template']);
            $template->preface = $preface;
            $template->initialize();
        } else {
            $template = Template::newInstance();
            $template->preface = $preface;
            $template->initialize();
        }
        
        // 加载配置中的所有service
        if(is_array($config) && array_key_exists('services', $config) && $config['services'] && is_array($config['services'])) {
            foreach($config['services'] as $k => $name) {
                $services[$name] = Service::newInstance($name);
                $services[$name]->initialize();
            }
        } else {
            $services['demo'] = Service::newInstance();
            $services['demo']->initialize();
        }
        
        return $this;
    }
    /**
     * 获取某一个Service对象
     *
     * @param string $name            
     * @return Service
     */
    protected function service($name) {
        $services = &$this->services;
        if(array_key_exists($name, $services)) {
            return $services[$name];
        } else if(is_string($name) && $name) {
            $services[$name] = Service::newInstance($name);
            $services[$name]->initialize();
            return $services[$name];
        } else {
            Debugger::warn('service name is empty or hasnot been autoinstantiated by service name:'.$name, 'SERVICE');
            return $services['demo'];
        }
    }
    /**
     * 默认执行方法
     */
    public function launch() {
    }
    /**
     * 路由执行方法
     *
     * @param string $method
     *            dispatch method,default is empty
     * @param array $params
     *            dispatch method arguments
     * @return Action $this
     */
    public function dispatch($method, $params) { // must return $this
        Debugger::info('dispatch', 'Action');
        $dispatchkey = Laywork::get('dispatch-key') || Action::DISPATCH_KEY;
        $dispatchstyle = Laywork::get('dispatch-style') || Action::DISPATCH_STYLE;
        
        if($method) {
            $dispatcher = $method;
        } else if(is_string($dispatchkey) || is_integer($dispatchkey)) {
            $variable = Scope::parseScope();
            $dispatcher = (array_key_exists($dispatchkey, $variable)) ? $_REQUEST[$dispatchkey] : false;
        } else {
            $ext = pathinfo($_SERVER['PHP_SELF']);
            $dispatcher = $ext['filename'];
        }
        if($dispatcher) {
            str_replace($search, $replace, $subject);
            $method = str_replace('*', $dispatcher, $dispatchstyle);
        }
        
        if(method_exists($this, $method) && $method != 'init' && $method != 'tail' && $method != 'dispatch' && substr($method, 0, 2) != '__') {
            $this->$method($params);
        } else {
            $this->launch($params);
        }
        
        return $this;
    }
    /**
     * 最后执行方法
     *
     * @return Action
     */
    public function tail() { // must return $this
        Debugger::info('tail', 'Action');
        extract(pathinfo($_SERVER['PHP_SELF']));
        
        $extension = isset($extension) ? $extension : '';
        switch($extension) {
            case 'json':
                $this->template->header('Content-Type: application/json');
                $this->template->header('Cache-Control: no-store');
                $this->template->json();
                break;
            case 'xml':
                $this->template->header('Content-Type: text/xml');
                $this->template->xml();
                break;
            default:
                $this->template->out();
        }
        return $this;
    }
}
?>