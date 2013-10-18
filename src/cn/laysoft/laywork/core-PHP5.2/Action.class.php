<?php
/**
 * 核心基础控制器
 * @see https://github.com/lay595715148/laywork3
 * 
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * <p>基础控制器</p>
 * <p>核心类，继承至此类的对象将会在运行时自动执行初始化init方法</p>
 * 
 * @abstract
 */
abstract class Action extends Base {
    const DISPATCH_KEY = 'a';
    const DISPATCH_STYLE = '*';
    /**
     * @staticvar action instance
     */
    private static $instance = null;
    /**
     * get action instance 
     * @param $name name of action
     * @param $config default is empty
     * @return Action
     */
    public static function newInstance($name = '', $config = '') {
        $config = is_array($config)?$config:Laywork::actionConfig($name);
        $classname = $config && isset($config['classname'])?$config['classname']:'DemoAction';
        Debugger::info("new action($classname) instance", 'Action', __LINE__, __METHOD__, __CLASS__);
        
        if(self::$instance == null) {
            if(isset($config['classname'])) {
                self::$instance = new $classname($config);
            } else {
                self::$instance = new DemoAction($config);
            }
            if(!(self::$instance instanceof Action)) {
                self::$instance = new DemoAction($config);
            }
        }
        return self::$instance;
    }
    
    /**
     * @var array 配置信息数组
     */
    protected $config = array();
    /**
     * @var array 存放配置的AbstractService对象
     */
    protected $services = array();
    /**
     * @var array 存放自注入的AbstractBean对象
     */
    //protected $beans = array();
    /**
     * @var AbstractTemplate 模板引擎对象
     */
    protected $template;
    /**
     * @var Preface 引语引擎对象
     */
    protected $preface;
    /**
     * 构造方法
     * @param array $config
     */
    protected function __construct($config = '') {
        $this->config = $config;
    }
    /**
     * 初始化
     * @return Action
     */
    public function initialize() {//must return $this
        Debugger::info("initialize", 'Action', __LINE__, __METHOD__, __CLASS__);
        $config      = &$this->config;
        $services    = &$this->services;
        $template    = &$this->template;
        $preface     = &$this->preface;

        //加载配置中的所有preface
        if(is_array($config) && array_key_exists('preface', $config)) {
            $preface = Preface::newInstance($config['preface']);
            $preface->initialize();
        } else {
            $preface = Preface::newInstance();
            $preface->initialize();
        }
        
        //加载配置中的所有service
        if(is_array($config) && array_key_exists('services',$config) && $config['services'] && is_array($config['services'])) {
            foreach($config['services'] as $k=>$name) {
                $services[$name] = Service::newInstance($name);
                $services[$name]->initialize();
            }
        } else {
            $services['demo'] = Service::newInstance();
            $services['demo']->initialize();
        }
        
        //加载配置中的所有template
        if(is_array($config) && array_key_exists('template', $config)) {
            $template = Template::newInstance($config['template']);
            $template->initialize();
            $template->preface = $preface;
        } else {
            $template = Template::newInstance();
            $template->initialize();
            $template->preface = $preface;
        }

        return $this;
    }
    /**
     * 获取以某一个Service对象
     * @param string $name
     * @return Service
     */
    protected function service($name) {
        $services = &$this->services;
        if(array_key_exists($name, $services)) {
            return $services[$name];
        } else if(is_string($name)) {
            $services[$name] = Service::newInstance($name);
            $services[$name]->initialize();
            return $services[$name];
        } else {
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
     * @param Exception $e 异常对象,默认为空
     * @return Action
     */
    public function dispatch($method, $params) {//must return $this
        Debugger::info('dispatch', 'Action', __LINE__, __METHOD__, __CLASS__);
        $dispatchkey = Laywork::get('dispatch-key') || Action::DISPATCH_KEY;
        $dispatchstyle = Laywork::get('dispatch-style') || Action::DISPATCH_STYLE;

        if($method) {
            $dispatcher = $method;
        } else if(is_string($dispatchkey) || is_integer($dispatchkey)) {
            $variable   = Scope::parseScope();
            $dispatcher = (array_key_exists($dispatchkey, $variable))?$_REQUEST[$dispatchkey]:false;
        } else {
            $ext        = pathinfo($_SERVER['PHP_SELF']);
            $dispatcher = $ext['filename'];
        }
        if($dispatcher) {
            $method = str_replace('*', $dispatcher, $dispatchstyle);
        }

        if(method_exists($this,$method) && $method != 'init' && $method != 'tail' && $method != 'dispatch' && substr($method,0,2) != '__') {
            $this->$method($params);
        } else {
            $this->launch($params);
        }
        
        return $this;
    }
    /**
     * 最后执行方法
     * @return Action
     */
    public function tail() {//must return $this
        Debugger::info('tail', 'Action', __LINE__, __METHOD__, __CLASS__);
        extract(pathinfo($_SERVER['PHP_SELF']));
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