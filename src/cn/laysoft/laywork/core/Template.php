<?php
/**
 * 模板引擎基础类
 * @author Lay Li
 * @Version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
use cn\laysoft\laywork\demo\DemoTemplate;
use Laywork,Debugger;
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * 模板引擎基础类
 * @abstract
 */
abstract class Template extends Base {
    const TAG_PROVIDER = 'template-provider';
    /**
     * @staticvar Template instance
     */
    private static $instance = null;
    /**
     * get Template instance 
     * @param $name name of Template
     * @param $config default is empty
     * @return Template
     */
    public static function newInstance($name = '') {
        if(is_array($name)) {
            Debugger::info("new template instance by config(json encoded):".json_encode($name), 'TEMPLATE');
        } else {
            Debugger::info("new template instance by name:$name", 'TEMPLATE');
        }
        
        if(self::$instance == null) {//增加provider功能
            $provider = Laywork::get(self::TAG_PROVIDER);
            if($provider && is_string($provider)) {
                $provider = new $provider();
            }
            if($provider instanceof ITemplateProvider) {
                self::$instance = $provider->provide($name);//执行provide方法
            } else if($provider) {
                Debugger::warn('given provider isnot an instance of ITemplateProvider', 'TEMPLATE');
            }
            //如果没有自定义实现ITemplateProvider接口的类对象，使用默认的配置项进行实现
            if(!(self::$instance instanceof Template)) {
                $config = is_array($name)?$name:Laywork::templateConfig($name);
                $classname = isset($config['classname'])?$config['classname']:'DemoTemplate';
                if(isset($config['classname'])) {
                    self::$instance = new $classname($config);
                }
                if(!(self::$instance instanceof Template)) {
                    Debugger::warn('template has been instantiated by default DemoTemplate', 'TEMPLATE');
                    self::$instance = new DemoTemplate($config);
                }
            }
        }
        return self::$instance;
    }
    /**
     * Preface对象
     * @var Preface $preface
     */
    public $preface;
    /**
     * 配置信息数组
     * @var array $config
     */
    protected $config = array();
    /**
     * 输出变量内容数组
     * @var array $vars
     */
    protected $vars = array();
    /**
     * HTTP headers
     * @var array $headers
     */
    protected $headers = array();
    /**
     * HTML metas
     * @var array $metas
     */
    protected $metas = array();
    /**
     * HTML scripts
     * @var array $jses
     */
    protected $jses = array();
    /**
     * HTML scripts in the end
     * @var array $javascript
     */
    protected $javascript = array();
    /**
     * HTML css links
     * @var array $csses
     */
    protected $csses = array();
    /**
     * file path
     * @var string $file
     */
    protected $file;
    /**
     * 构造方法
     * @param array $config 配置信息数组
     */
    public function __construct($config = '') {
        $this->config = $config;
    }
    /**
     * 初始化
     */
    public function initialize() {//must return $this
        Debugger::info('initialize', 'TEMPLATE');
        return $this;
    }
    /**
     * push header for output
     * @param string $header http header string
     */
    public function header($header) {
        $headers   = &$this->headers;
        $headers[] = $header;
    }
    /**
     * set title ,if $append equal false, then reset title;if $append equal 1 or true,
     * then append end position; other append start position
     * @param string $str title
     * @param boolean $append if append
     */
    public function title($str, $append = false) {
        $vars  = &$this->vars;
        $title = isset($vars['title'])?$vars['title']:false;
        if(!$title || $append === false) {
            $vars['title'] = $str;
        } else if($append && $append === 1) {
            $vars['title'] = $title.$str;
        } else {
            $vars['title'] = $str.$title;
        }
    }
    /**
     * push variables with a name
     * @param string $name name of variable
     * @param mixed $value value of variable
     */
    public function push($name, $value) {
        $vars        = &$this->vars;
        $vars[$name] = $value;
    }
    /**
     * set include file path
     * @param string $filepath file path
     */
    public function file($filepath) {
        global $_ROOTPATH;
        if(strpos($filepath, $_ROOTPATH) === 0) {
            $this->file = $filepath;
        } else {
            $this->file = $_ROOTPATH.$filepath;
        }
    }
    /**
     * set include theme template file path
     * @param string $filepath template file path, relative template theme directory
     */
    public function plate($filepath) {
        global $_ROOTPATH;
        $filepath = str_replace("\\", "/", $filepath);
        if(strpos($filepath, $_ROOTPATH) === 0) {
            $this->file = $filepath;
        } else {
            $themes = Laywork::get('themes');
            $theme = Laywork::get('theme');
            if($themes && $theme && array_key_exists($theme, $themes)) {
                if(!isset($themes[$theme]['dir'])) $themes[$theme]['dir'] = '';
                $this->file = $_ROOTPATH.$themes[$theme]['dir'].$filepath;
            } else {
                $this->file = $_ROOTPATH.$filepath;
            }
        }
    }
    /**
     * set meta infomation
     * @param array $meta array for html meta tag
     */
    public function meta($meta) {
        $metas = &$this->metas;
        if(is_array($meta)) {
            foreach($meta as $i=>$m) {
                $metas[] = $m;
            }
        } else {
            $metas[] = $meta;
        }
    }
    /**
     * set include js path
     * @param string $js javascript file src path in html tag script
     */
    public function js($js) {
        $jses   = &$this->jses;
        if(is_array($js)) {
            foreach($js as $i=>$j) {
                $jses[] = $j;
            }
        } else {
            $jses[] = $js;
        }
    }
    /**
     * set include js path,those will echo in end of document
     * @param string $js javascript file src path in html tag script
     */
    public function javascript($js) {
        $javascript   = &$this->javascript;
        if(is_array($js)) {
            foreach($js as $i=>$j) {
                $javascript[] = $j;
            }
        } else {
            $javascript[] = $js;
        }
    }
    /**
     * set include css path
     * @param string $css css file link path
     */
    public function css($css) {
        $csses   = &$this->csses;
        if(is_array($css)) {
            foreach($css as $i=>$c) {
                $csses[] = $c;
            }
        } else {
            $csses[] = $css;
        }
    }
    /**
     * get template variables,
     * return the point of template variables
     * @return array
     */
    public function vars() {
        Debugger::info('variable', 'TEMPLATE');
        $templateVars = &$this->vars;

        return $templateVars;
    }
    /**
     * output as json string
     */
    public function json() {
        Debugger::info('json', 'TEMPLATE');
        $headers      = &$this->headers;
        $templateVars = &$this->vars;
        $templateVars = array_diff_key($templateVars,array('title'=>1));
        foreach($headers as $header) {
            header($header);
        }
        echo json_encode($templateVars);
    }
    /**
     * output as xml string
     */
    public function xml() {
        Debugger::info('xml', 'TEMPLATE');
        $headers      = &$this->headers;
        $templateVars = &$this->vars;
        $templateVars = array_diff_key($templateVars,array('title'=>1));
        foreach($headers as $header) {
            header($header);
        }
        echo Parser::array2XML($templateVars);
    }
    /**
     * output as template
     * @return void
     */
    public function out() {
        $this->display();
    }
    /**
     * output as template
     * @return void
     */
    public function display() {
        Debugger::info('display', 'TEMPLATE');
        $templateVars = &$this->vars;
        $templateFile = &$this->file;
        $metas        = &$this->metas;
        $jses         = &$this->jses;
        $javascript   = &$this->javascript;
        $csses        = &$this->csses;
        $headers      = &$this->headers;

        extract($templateVars);
        foreach($headers as $header) {
            header($header);
        }
        if(file_exists($templateFile)) {
            include($templateFile);
        }
    }
}
?>
