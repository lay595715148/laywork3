<?php
/**
 * 模板引擎基础类
 * @author Lay Li
 * @Version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
use cn\laysoft\laywork\demo\DemoTemplate;
use Laywork;
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * 模板引擎基础类
 * @abstract
 */
abstract class Template extends Base {
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
    public static function newInstance($config = '') {
        if(self::$instance == null) {
            self::$instance = new DemoTemplate($config);
        }
        return self::$instance;
    }
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
     * @var array $config
     */
    protected $headers = array();
    /**
     * HTML metas
     * @var array $config
     */
    protected $metas = array();
    /**
     * HTML scripts
     * @var array $config
     */
    protected $jses = array();
    /**
     * HTML scripts in the end
     * @var array $config
     */
    protected $javascript = array();
    /**
     * HTML css links
     * @var array $config
     */
    protected $csses = array();
    /**
     * file path
     * @var array $config
     */
    protected $file;
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
        global $_CFG,$_SRCPath;
        $lang = &$_CFG['language'];
        $langs = &$_CFG['languages'];
        if($lang && array_key_exists($lang,$langs) && file_exists($_SRCPath.$langs[$lang])) {
            include $_SRCPath.$langs[$lang];
        }
        
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
        $this->file = $filepath;
    }
    /**
     * set include theme template file path
     * @param string $filepath template file path
     */
    public function template($filepath) {
        global $_CFG,$_SRCPath;
        $theme = &$_CFG['theme']['theme-use'];
        $themes = &$_CFG['themes'];
        if(array_key_exists($theme,$themes)) {
            $this->file = $_SRCPath.$_CFG['theme']['theme-dir'].$themes[$theme]['tpl'].$filepath;
        } else {
            $this->file = $filepath;
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
     * output as json
     */
    public function json() {
        $headers      = &$this->headers;
        $templateVars = &$this->vars;
		$templateVars = array_diff_key($templateVars,array('title'=>1));
        foreach($headers as $header) {
            header($header);
        }
        echo json_encode($templateVars);
    }
    /**
     * output as xml
     */
    public function xml() {
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
     */
    public function out() {
        global $_SRCPath,$_CFG,$_LAN;
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
