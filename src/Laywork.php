<?php
/**
 * Laywork主类
 * @see https://github.com/lay595715148/laywork3
 * 
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */

use cn\laysoft\laywork\core\Action;//if using namespace
//use cn\laysoft\laywork\core\Service;
//use cn\laysoft\laywork\core\Store;
//use cn\laysoft\laywork\core\Bean;
if(!defined('INIT_LAYWORK')) { exit; }

global $_LAYWORKPATH,$_ROOTPATH;

$_ROOTPATH = $_LAYWORKPATH = str_replace("\\", "/", dirname(__DIR__));//Returns parent directory's path


/**
 * <p>Laywork主类</p>
 * 
 * @author Lay Li
 */
final class Laywork {
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
        'Mysql' => '/cn/laysoft/laywork/core/Mysql.class.php',
        'Arrange' => '/cn/laysoft/laywork/core/Arrange.class.php',
        'Cell' => '/cn/laysoft/laywork/core/Cell.class.php',
        'Condition' => '/cn/laysoft/laywork/core/Condition.class.php',
        'TableBean' => '/cn/laysoft/laywork/core/TableBean.class.php',
        'Template' => '/cn/laysoft/laywork/core/Template.class.php',
        'DemoAction' => '/cn/laysoft/laywork/demo/DemoAction.class.php',
        'DemoService' => '/cn/laysoft/laywork/demo/DemoService.class.php',
        'DemoStore' => '/cn/laysoft/laywork/demo/DemoStore.class.php',
        'DemoTemplate' => '/cn/laysoft/laywork/demo/DemoTemplate.class.php'
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
        self::$debug = $debug;
    }
    /**
     * class autoload function
     * @param $classname autoload class name
     * @return void
     */
    public static function autoload($classname) {
        global $_LAYWORKPATH;
        $_CLASSPATH = $_LAYWORKPATH.'/src';
        $classes = &self::$classes;
        $suffixes = array('.php','.class.php','.inc');

        if(array_key_exists($classname, $classes)) {//全名映射
            if(is_file($classes[$classname])) {
                if(self::$debug) echo 'require_once '.$classes[$classname].'<br>';
                require_once $classes[$classname];
            } else if(is_file($_CLASSPATH.$classes[$classname])) {
                if(self::$debug) echo 'require_once '.$_CLASSPATH.$classes[$classname].'<br>';
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
                            if(self::$debug) echo 'require_once '.$tmppath.$suffix.'<br>';
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
                                if(self::$debug) echo 'require_once '.$tmppath.$suffix.'<br>';
                                require_once $tmppath.$suffix;
                                break 2;
                            }
                        }
                        continue;
                    } else if($index == count($matches[1]) - 1) {
                        foreach($suffixes as $i=>$suffix) {
                            if(is_file($path.$suffix)) {
                                if(self::$debug) echo 'require_once '.$path.$suffix.'<br>';
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
     * get configuration by key string
     * @param $keystr key string, example: 'action.index'
     * @param $default if nothing by $keystr,the default value
     * @return mixed
     */
    public static function get($keystr, $default = null) {
        $node = &self::$configuration;
        if(is_string($keystr) && $keystr) {
            $keys = explode('.', $keystr);
            foreach($keys as $key) {
                if(isset($node[$key])) {
                    $node = &$node[$key];
                } else {
                    return $default;
                }
            }
        }
        return $node;
    }
    /**
     * set configuration
     * @param $name
     * @param $value
     * @return void
     */
    public static function set($keystr, $value){
        //self::$configuration[$name] = $value;
        
        $node = &self::$configuration;
        if(is_string($keystr) && $keystr) {
            $keys = explode('.', $keystr);
            $count = count($keys);
            foreach($keys as $index=>$key) {
                if(isset($node[$key]) && $index === $count - 1) {
                    //TODO warning has been configured by this name
                    $node[$key] = $value;
                } else if(isset($node[$key])) {
                    $node = &$node[$key];
                } else if($index === $count - 1) {
                    $node[$key] = $value;
                } else {
                    $node[$key] = array();
                    $node = &$node[$key];
                }
            }
        }
    }
    /**
     * configure an action
     * @param $name
     * @param $config
     * @return void
     */
    public static function action($name, $config) {
        $actions = &self::get('actions');
        //echo '<pre>';print_r($config);echo '</pre>';
        
        if(array_key_exists($name, $actions)) {
            //print_r($name);print_r($config);echo '<br>';
            //TODO warning has been configured by this name
        } else if(is_string($name) || is_numeric($name)) {
            if(self::$debug) echo 'do_configure an action'.'<br>';
            //$actions[$name] = $config;
            self::set('actions.'.$name, $config);
            //TODO configure an action
        }
        //echo '<pre>';print_r(self::get());echo '</pre>';
    }
    /**
     * configure a service
     * @param $name
     * @param $config
     * @return void
     */
    public static function service($name, $config) {
        $services = &self::get('services');
        
        if(array_key_exists($name, $services)) {
            //TODO warning has been configured by this name
        } else {
            if(self::$debug) echo 'do_configure a service'.'<br>';
            //$services[$name] = $config;
            self::set('services.'.$name, $config);
            //TODO configure a service
        }
    }
    /**
     * configure a store
     * @param $name
     * @param $config
     * @return void
     */
    public static function store($name, $config) {
        $stores = &self::get('stores');
        
        if(array_key_exists($name, $stores)) {
            //TODO warning has been configured by this name
        } else {
            if(self::$debug) echo 'do_configure a store'.'<br>';
            //$stores[$name] = $config;
            self::set('stores.'.$name, $config);
            //TODO configure a store
        }
    }
    /**
     * configure a bean
     * @param $name
     * @param $config
     * @return void
     */
    public static function bean($name, $config) {
        $beans = &self::get('beans');
        $config = is_array($config)?$config:array();
        
        if(array_key_exists($name, $beans)) {
            //TODO warning has been configured by this name
        } else {
            if(self::$debug) echo 'do_configure a bean'.'<br>';
            //$beans[$name] = $config;
            self::set('beans.'.$name, $config);
            //TODO configure a bean
        }
    }
    /**
     * get bean configuration by name
     * @param $name
     * @return array
     */
    public static function actionConfig($name) {
        return self::get('actions.'.$name);
    }
    /**
     * get bean configuration by name
     * @param $name
     * @return array
     */
    public static function serviceConfig($name) {
        return self::get('services.'.$name);
    }
    /**
     * get bean configuration by name
     * @param $name
     * @return array
     */
    public static function storeConfig($name) {
        return self::get('stores.'.$name);
    }
    /**
     * get bean configuration by name
     * @param $bean
     * @return array
     */
    public static function beanConfig($name) {
        return self::get('beans.'.$name);
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
        $configurations = &self::$configuration;
        if(is_array($configuration) && !$isFile) {
            foreach($configuration as $key=>$item) {
                if(is_string($key) && $key) {//key is not null
                    switch($key) {
                        case 'actions':
                            if(is_array($item)) {
                                foreach($item as $name=>$conf) {
                                    self::action($name, $conf);
                                }
                            } else {
                                //TODO warning actions is not an array
                            }
                            break;
                        case 'services':
                            if(is_array($item)) {
                                foreach($item as $name=>$conf) {
                                    self::service($name, $conf);
                                }
                            } else {
                                //TODO warning services is not an array
                            }
                            break;
                        case 'stores':
                            if(is_array($item)) {
                                foreach($item as $name=>$conf) {
                                    self::store($name, $conf);
                                }
                            } else {
                                //TODO warning stores is not an array
                            }
                            break;
                        case 'beans':
                            if(is_array($item)) {
                                foreach($item as $name=>$conf) {
                                    self::bean($name, $conf);
                                }
                            } else {
                                //TODO warning beans is not an array
                            }
                            break;
                        case 'files':
                            if(is_array($item)) {
                                foreach($item as $file) {
                                    self::configure($file);
                                }
                            } else {
                                //TODO warning beans is not an array
                            }
                            break;
                        default:
                            self::set($key, $item);
                            //TODO default
                            break;
                    }
                } else {
                    self::set($key, $item);
                    //TODO default
                }
            }
        } else if(is_array($configuration)) {
            if(!empty($configuration)) {
                foreach($configuration as $index=>$configfile) {
                    self::configure($configfile);
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
                self::configure($tmparr);
            } else {
                self::configure($tmparr, false);
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
        self::getInstance()->run($action, $method, $params);
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
    /**
     * run application
     */
    public function run($action, $method, $params) {
        global $_LOADPATH,$_CLASSPATH,$_ROOTPATH,$_LAYWORKPATH;
        /*echo '$_LOADPATH: '.$_LOADPATH.'<br>';
        echo '$_CLASSPATH: '.$_CLASSPATH.'<br>';
        echo '$_ROOTPATH: '.$_ROOTPATH.'<br>';
        echo '$_LAYWORKPATH: '.$_LAYWORKPATH.'<br>';*/
        
        
        if(!is_string($action) || !$action) {
            extract(pathinfo($_SERVER['PHP_SELF']));//generate $dirname,$basename, $extension, $filename
            $obj = Action::newInstance($filename);
        } else {
            $obj = Action::newInstance($action);
        }
        
        $obj->initialize();
        $obj->dispatch($method);
        $obj->tail();
        /*Service::newInstance();
        Store::newInstance();
        $m = Bean::newInstance();
        echo '<pre>';print_r($m->rowsToArray(array(array('id'=>'21.22sd', 'datetime'=>'2011-10-31 03:16:40', 'type'=>'2'), array('id'=>'12sd', 'name'=>123, 'datetime'=>1320002200))));echo '</pre>';*/
    }
}

class_alias('Laywork', 'W');//Layload class alias
?>