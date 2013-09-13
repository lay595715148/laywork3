<?php
if(!defined('INIT_LAYWORK')) { exit; }

use cn\laysoft\laywork\core\Action;
use cn\laysoft\laywork\core\Service;

global $_LAYWORKPATH,$_ROOTPATH;

$_ROOTPATH = $_LAYWORKPATH = str_replace("\\", "/", dirname(__DIR__));//Returns parent directory's path


/**
 * <p>Laywork主类</p>
 * 
 * @author Lay Li
 */
class Laywork {
    /**
     * @staticvar debug
     */
    public static $debug = false;
    
    /**
     * @staticvar configuration
     */
    public static $configuration = array(
        'actions' => array(),
        'services' => array(),
        'stores' => array(),
        'beans' => array()
    );
    /**
     * @staticvar all class mappings
     */
    public static $classes = array(
        'Base' => '/cn/laysoft/laywork/core/Base.class.php',
        'Bean' => '/cn/laysoft/laywork/core/Bean.class.php',
        'Action' => '/cn/laysoft/laywork/core/Action.class.php',
        'Scope' => '/cn/laysoft/laywork/core/Scope.class.php',
        'Service' => '/cn/laysoft/laywork/core/Service.class.php',
        'Store' => '/cn/laysoft/laywork/core/Store.class.php',
        'TableBean' => '/cn/laysoft/laywork/core/TableBean.class.php',
        'Template' => '/cn/laysoft/laywork/core/Template.class.php',
        'DemoAction' => '/cn/laysoft/laywork/demo/DemoAction.class.php',
        'DemoService' => '/cn/laysoft/laywork/demo/DemoService.class.php'
    );
    /**
     * set laywork path
     * @param $layworkpath laywork directory path,default is empty
     * @return void
     */
    public static function rootpath($rootpath = '') {
        global $_ROOTPATH;
        $_ROOTPATH = str_replace("\\", "/", is_dir($rootpath)?$rootpath:dirname(__DIR__));
    }
    /**
     * set laywork root path
     * @param $layworkpath laywork directory path,default is empty
     * @return void
     */
    public static function layworkpath($layworkpath = '') {
        global $_LAYWORKPATH;
        $_LAYWORKPATH = str_replace("\\", "/", is_dir($layworkpath)?$layworkpath:dirname(__DIR__));
    }
    /**
     * initialize autoload function
     * @return void
     */
    public static function initialize($debug = false) {
        spl_autoload_register('Laywork::autoload');
        Laywork::$debug = $debug;
    }
    /**
     * class autoload function
     * @param $classname autoload class name
     * @return void
     */
    public static function autoload($classname) {
        global $_LAYWORKPATH;
        $_CLASSPATH = $_LAYWORKPATH.'/src';
        $classes = &Laywork::$classes;
        $suffixes = array('.php','.class.php','.inc');

        if(array_key_exists($classname, $classes)) {//全名映射
            if(is_file($classes[$classname])) {
                if(Laywork::$debug) echo 'require_once '.$classes[$classname].'<br>';
                require_once $classes[$classname];
            } else if(is_file($_CLASSPATH.$classes[$classname])) {
                if(Laywork::$debug) echo 'require_once '.$_CLASSPATH.$classes[$classname].'<br>';
                require_once $_CLASSPATH.$classes[$classname];
            } else {
                //TODO mapping is error
            }
        } else {
            $tmparr = explode("\\",$classname);
            if(count($tmparr) > 1) {//if is namespace
                $name = array_pop($tmparr);
                $path = $_CLASSPATH.'/'.implode('/', $tmparr);
                $required = false;
                //命名空间文件夹查找
                if(is_dir($path)) {
                    $tmppath = $path.'/'.$name;
                    foreach($suffixes as $i=>$suffix) {
                        if(is_file($tmppath.$suffix)) {
                            if(Laywork::$debug) echo 'require_once '.$tmppath.$suffix.'<br>';
                            require_once $tmppath.$suffix;
                            $required = true;
                            break;
                        }
                    }
                } else {
                    //TODO not found by namespace dir
                }
            } else if(preg_match_all('/([A-Z]{1,}[a-z0-9]{0,}|[a-z0-9]{1,})_{0,1}/', $classname, $matches)) {
                //TODO autoload class by regular
                $path = $_CLASSPATH;
                foreach($matches[1] as $index=>$item) {
                    $path .= '/'.$item;
                    if(is_dir($path)) {//顺序文件夹查找
                        $tmppath = $path.'/'.substr($classname, strpos($classname, $item) + strlen($item));
                        echo $tmppath.'<br>';
                        foreach($suffixes as $i=>$suffix) {
                            if(is_file($tmppath.$suffix)) {
                                if(Laywork::$debug) echo 'require_once '.$tmppath.$suffix.'<br>';
                                require_once $tmppath.$suffix;
                                break 2;
                            }
                        }
                        continue;
                    } else if($index == count($matches[1]) - 1) {
                        foreach($suffixes as $i=>$suffix) {
                            if(is_file($path.$suffix)) {
                                if(Laywork::$debug) echo 'require_once '.$path.$suffix.'<br>';
                                require_once $path.$suffix;
                                break 2;
                            }
                        }
                        break;
                    } else {
                        //TODO not found by regular match
                    }
                }
            }
        }
    }
    /**
     * configure an action
     * @param $action
     * @return void
     */
    public static function action($name, $config) {
        $actions = &Laywork::$configuration['actions'];
        
        if(array_key_exists($name, $actions)) {
            //print_r($name);print_r($config);echo '<br>';
            //TODO warning has been configured by this name
        } else {
            if(Laywork::$debug) echo 'configure an action'.'<br>';
            $actions[$name] = $config;
            //TODO configure an action
        }
    }
    /**
     * configure a service
     * @param $service
     * @return void
     */
    public static function service($name, $config) {
        $services = &Laywork::$configuration['services'];
        
        if(array_key_exists($name, $services)) {
            //TODO warning has been configured by this name
        } else {
            if(Laywork::$debug) echo 'configure a service'.'<br>';
            $services[$name] = $config;
            //TODO configure a service
        }
    }
    /**
     * configure a store
     * @param $store
     * @return void
     */
    public static function store($name, $config) {
        $stores = &Laywork::$configuration['stores'];
        
        if(array_key_exists($name, $stores)) {
            //TODO warning has been configured by this name
        } else {
            if(Laywork::$debug) echo 'configure a store'.'<br>';
            $stores[$name] = $config;
            //TODO configure a store
        }
    }
    /**
     * configure a bean
     * @param $bean
     * @return void
     */
    public static function bean($name, $config) {
        $beans = &Laywork::$configuration['beans'];
        $config = is_array($config)?$config:array();
        
        if(array_key_exists($name, $beans)) {
            //TODO warning has been configured by this name
        } else {
            if(Laywork::$debug) echo 'configure a bean'.'<br>';
            $beans[$name] = $config;
            //TODO configure a bean
        }
    }
    /**
     * get bean configuration by name
     * @param $bean
     * @return array
     */
    public static function actionConfig($name) {
        $actions = &Laywork::$configuration['actions'];
        
        if(array_key_exists($name, $actions)) {
            return $actions[$name];
        } else {
            //TODO no action config by this name
            return ;
        }
    }
    /**
     * get bean configuration by name
     * @param $bean
     * @return array
     */
    public static function serviceConfig($name) {
        $services = &Laywork::$configuration['services'];
        
        if(array_key_exists($name, $services)) {
            return $services[$name];
        } else {
            //TODO no action config by this name
            return ;
        }
    }
    /**
     * get bean configuration by name
     * @param $bean
     * @return array
     */
    public static function storeConfig($name) {
        $stores = &Laywork::$configuration['stores'];
        
        if(array_key_exists($name, $stores)) {
            return $stores[$name];
        } else {
            //TODO no action config by this name
            return ;
        }
    }
    /**
     * get bean configuration by name
     * @param $bean
     * @return array
     */
    public static function beanConfig($name) {
        $beans = &Laywork::$configuration['beans'];
        
        if(array_key_exists($name, $beans)) {
            return $beans[$name];
        } else {
            //TODO no action config by this name
            return ;
        }
    }
    /**
     * laywork autorun configuration,all config file is load in $_ROOTPATH
     * include actions,services,stores,beans,files...other
     * @param $configuration a file or file array or config array
     * @param $isFile sign file,default is true
     * @return void
     */
    public static function configure($configuration, $isFile = true) {
        global $_ROOTPATH;
        $configurations = &Laywork::$configuration;
        if(is_array($configuration) && !$isFile) {
            foreach($configuration as $key=>$item) {
                switch($key) {
                    case 'actions':
                        if(is_array($item)) {
                            foreach($item as $name=>$conf) {
                                Laywork::action($name, $conf);
                            }
                        } else {
                            //TODO warning actions is not an array
                        }
                        break;
                    case 'services':
                        if(is_array($item)) {
                            foreach($item as $name=>$conf) {
                                Laywork::service($name, $conf);
                            }
                        } else {
                            //TODO warning services is not an array
                        }
                        break;
                    case 'stores':
                        if(is_array($item)) {
                            foreach($item as $name=>$conf) {
                                Laywork::store($name, $conf);
                            }
                        } else {
                            //TODO warning stores is not an array
                        }
                        break;
                    case 'beans':
                        if(is_array($item)) {
                            foreach($item as $name=>$conf) {
                                Laywork::bean($name, $conf);
                            }
                        } else {
                            //TODO warning beans is not an array
                        }
                        break;
                    case 'files':
                        if(is_array($item)) {
                            foreach($item as $file) {
                                Laywork::configure($file);
                            }
                        } else {
                            //TODO warning beans is not an array
                        }
                        break;
                    default:
                        break;
                }
            }
        } else if(is_array($configuration)) {
            if(!empty($configuration)) {
                foreach($configuration as $index=>$configfile) {
                    Laywork::configure($configfile);
                }
            }
        } else {
            if(is_file($configuration)) {
                $tmparr = include_once $configuration;
            } else if(is_file($_ROOTPATH.$configuration)) {
                $tmparr = include_once $_ROOTPATH.$configuration;
            } else {
                $tmparr = array();
            }
            
            if(empty($tmparr)) {
                Laywork::configure($tmparr);
            } else {
                Laywork::configure($tmparr, false);
            }
        }
    }
    /**
     * start laywork
     * @param $action action name,default is empty
     * @param $method method name,default is empty
     * @param $params param array,default is empty
     */
    public static function start($action = '', $method = '', $params = '') {
        Laywork::getInstance()->run($action, $method, $params);
    }
    
    /**
     * @var Laywork 自身的一个实例对象
     */
    private static $instance = null;
    /**
     * 私有的构造方法
     */
    private function __construct() { }
    /**
     * 获取一个实例
     */
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new Laywork();
        }
        return self::$instance;
    }
    public function run($action, $method, $params) {
        global $_LOADPATH,$_CLASSPATH,$_ROOTPATH,$_LAYWORKPATH;
        echo '$_LOADPATH: '.$_LOADPATH.'<br>';
        echo '$_CLASSPATH: '.$_CLASSPATH.'<br>';
        echo '$_ROOTPATH: '.$_ROOTPATH.'<br>';
        echo '$_LAYWORKPATH: '.$_LAYWORKPATH.'<br>';
        
        Action::newInstance($action);
        Service::newInstance();
    }
}

?>