<?php
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * <p>基础控制器</p>
 * <p>核心类，继承至此类的对象将会在运行时自动执行初始化init方法</p>
 * 
 * @abstract
 */
abstract class Action extends Base {
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
    protected $beans = array();
    /**
     * @var AbstractTemplate 模板引擎对象
     */
    protected $template;
    /**
     * @var array 访问路径信息数据
     */
    protected $pathinfo = array();
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
    public function init() {//must return $this
        $this->pathinfo = pathinfo($_SERVER['PHP_SELF']);
        
        $config      = &$this->config;
        $services    = &$this->services;
        $beans       = &$this->beans;
        $template    = &$this->template;
        $serviceGen  = new DefaultServiceGen();
        $beanGen     = new DefaultBeanGen();
        $templateGen = new DefaultTemplateGen();

        if(is_array($config) && array_key_exists('services',$config) && $config['services'] && is_array($config['services'])) {
            //加载配置中的所有service
            foreach($config['services'] as $k=>$v) {
                $service = $serviceGen->genService($v)->init();
                $services[$v] = $service;
            }
        } else {
            $service = $serviceGen->genService()->init();
            $services[] = $service;
        }
        if(is_array($config) && array_key_exists('beans',$config) && $config['beans'] && is_array($config['beans'])) {
            //加载配置中的所有bean
            foreach($config['beans'] as $k=>$v) {
                $bean = $beanGen->genBean($v);
                $beans[$v] = $bean;
            }
        } else {
            $bean = $beanGen->genBean();
            $beans[] = $bean;
        }
        $template = $templateGen->genTemplate()->init();

        return $this;
    }
    /**
     * 默认执行方法
     */
    public function launch() {
    }
    /**
     * 路由执行方法
     * @param Exception $e 异常对象,默认为空
     */
    public function dispatch($e = null) {//must return $this
        global $_CFG;

        $dispatchkey = $_CFG['action']['dispatch-key'];
        $style       = $_CFG['action']['dispatch-style'];
        $method      = $_CFG['action']['dispatch-method'];
        $scope       = $_CFG['action']['dispatch-scope'];

        if($dispatchkey) {
            $variable   = Scope::parseScope((is_numeric($scope) && $scope >= 0 && $scope <= 5)?$scope:0);
            $dispatcher = (array_key_exists($dispatchkey,$variable))?$_REQUEST[$dispatchkey]:false;
        } else {
            $ext        = pathinfo($_SERVER['PHP_SELF']);
            $dispatcher = $ext['filename'];
        }
        if($dispatcher) {
            $method = str_replace('*',$dispatcher,$style);
        }

        if(method_exists($this,$method) && $method != 'init' && $method != 'tail' && $method != 'dispatch' && substr($method,0,2) != '__') {
            $this->$method();
        } else if($e){
            $this->launch($e);
        } else {
            $this->launch();
        }
        
        return $this;
    }
    /**
     * 最后执行方法
     */
    public function tail() {//must return $this
        $ext = &$this->pathinfo;
        $extension = array_key_exists('extension',$ext)?$ext['extension']:'';
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