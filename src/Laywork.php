<?php
/**
 * Laywork主类
 * @see https://github.com/lay595715148/laywork3
 * 
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */

use cn\laysoft\laywork\core\Action;//if using namespace
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
        'Base' => '/cn/laysoft/laywork/core-PHP5.2/Base.class.php',
        'Bean' => '/cn/laysoft/laywork/core-PHP5.2/Bean.class.php',
        'Action' => '/cn/laysoft/laywork/core-PHP5.2/Action.class.php',
        'Scope' => '/cn/laysoft/laywork/core-PHP5.2/Scope.class.php',
        'Service' => '/cn/laysoft/laywork/core-PHP5.2/Service.class.php',
        'Store' => '/cn/laysoft/laywork/core-PHP5.2/Store.class.php',
        'Mysql' => '/cn/laysoft/laywork/core-PHP5.2/Mysql.class.php',
        'PdoStore' => '/cn/laysoft/laywork/core-PHP5.2/PdoStore.class.php',
        'Arrange' => '/cn/laysoft/laywork/core-PHP5.2/Arrange.class.php',
        'Cell' => '/cn/laysoft/laywork/core-PHP5.2/Cell.class.php',
        'Condition' => '/cn/laysoft/laywork/core-PHP5.2/Condition.class.php',
        'TableBean' => '/cn/laysoft/laywork/core-PHP5.2/TableBean.class.php',
        'Template' => '/cn/laysoft/laywork/core-PHP5.2/Template.class.php',
        'Parser' => '/cn/laysoft/laywork/core-PHP5.2/Parser.class.php',
        'DemoAction' => '/cn/laysoft/laywork/demo-PHP5.2/DemoAction.class.php',
        'DemoBean' => '/cn/laysoft/laywork/demo-PHP5.2/DemoBean.class.php',
        'DemoService' => '/cn/laysoft/laywork/demo-PHP5.2/DemoService.class.php',
        'DemoStore' => '/cn/laysoft/laywork/demo-PHP5.2/DemoStore.class.php',
        'DemoTemplate' => '/cn/laysoft/laywork/demo-PHP5.2/DemoTemplate.class.php',
        
        'Util' => '/cn/laysoft/laywork/util-PHP5.2/Util.class.php'
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
        Debugger::initialize($debug);
        Debugger::info('APPLICATION', 'initilize application', __CLASS__, __METHOD__, __LINE__);
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
                require_once $classes[$classname];
                Debugger::info('REQUIRE_ONCE', $classes[$classname], __CLASS__, __METHOD__, __LINE__);
            } else if(is_file($_CLASSPATH.$classes[$classname])) {
                require_once $_CLASSPATH.$classes[$classname];
                Debugger::info('REQUIRE_ONCE', $_CLASSPATH.$classes[$classname], __CLASS__, __METHOD__, __LINE__);
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
                            Debugger::info('REQUIRE_ONCE', $tmppath.$suffix, __CLASS__, __METHOD__, __LINE__);
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
                        foreach($suffixes as $i=>$suffix) {
                            if(is_file($tmppath.$suffix)) {
                                Debugger::info('REQUIRE_ONCE', $tmppath.$suffix, __CLASS__, __METHOD__, __LINE__);
                                require_once $tmppath.$suffix;
                                break 2;
                            }
                        }
                        continue;
                    } else if($index == count($matches[1]) - 1) {
                        foreach($suffixes as $i=>$suffix) {
                            if(is_file($path.$suffix)) {
                                Debugger::info('REQUIRE_ONCE', $path.$suffix, __CLASS__, __METHOD__, __LINE__);
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
        if(!class_exists($classname) && !interface_exists($classname)) {
            throw new AutoloadException('class:'.$classname.' autoload error');
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
        
        if(array_key_exists($name, $actions)) {
            //TODO warning has been configured by this name
        } else if(is_string($name) || is_numeric($name)) {
            Debugger::info('DO_CONFIGURE', 'action('.$name.')', __CLASS__, __METHOD__, __LINE__);
            self::set('actions.'.$name, $config);
            //TODO configure an action
        }
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
            Debugger::info('DO_CONFIGURE', 'service('.$name.')', __CLASS__, __METHOD__, __LINE__);
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
            Debugger::info('DO_CONFIGURE', 'store('.$name.')', __CLASS__, __METHOD__, __LINE__);
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
            Debugger::info('DO_CONFIGURE', 'bean('.$name.')', __CLASS__, __METHOD__, __LINE__);
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
        Debugger::info('APPLICATION', 'start application', __CLASS__, __METHOD__, __LINE__);
        try {
            self::getInstance()->run($action, $method, $params);
        } catch (Exception $e) {
            Debugger::error('Exception', $e->getMessage()."\r\n".$e->getTraceAsString(), __CLASS__, __METHOD__, __LINE__);
        }
        Debugger::info('APPLICATION', 'finish application', __CLASS__, __METHOD__, __LINE__);
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
        
        if(!is_string($action) || !$action) {
            //generate $dirname,$basename, $extension, $filename
            extract(pathinfo($_SERVER['PHP_SELF']));
            $obj = Action::newInstance($filename);
        } else {
            $obj = Action::newInstance($action);
        }
        
        $obj->initialize();
        $obj->dispatch($method, $params);
        $obj->tail();
    }
}

class_alias('Laywork', 'W');//Layload class alias

class Debugger {
    const DEBUG_LEVEL_DEBUG = 1;
    const DEBUG_LEVEL_INFO = 2;
    const DEBUG_LEVEL_WARN = 4;
    const DEBUG_LEVEL_ERROR = 8;
    public static $out = true;
    public static $log = false;
    public static function initialize($debug) {
        if(!$debug || $debug === true) {
        } else if(is_array($debug)) {
            $debug['out'] = isset($debug['out']) ? $debug['out'] : isset($debug[0])?$debug[0]:false;
            $debug['log'] = isset($debug['log']) ? $debug['log'] : isset($debug[1])?$debug[1]:false;
            self::$out = ($debug['out'] === true)?true:intval($debug['out']);
            self::$log = ($debug['log'] === true)?true:intval($debug['log']);
        } else if(!is_int($debug)) {
            self::$out = self::$log = false;
        } else {
            self::$out = self::$log = $debug;
        }
    }
    public static function debug($tag, $msg, $classname = '', $method = '', $line = '') {
        if(self::$out === true || (self::$out && in_array(self::$out, array(1, 3, 5, 7, 9, 11, 13, 15)))) self::pre($tag, self::DEBUG_LEVEL_DEBUG, $msg, $classname, $method, $line);
        if(self::$log === true || (self::$log && in_array(self::$log, array(1, 3, 5, 7, 9, 11, 13, 15)))) self::log($tag, self::DEBUG_LEVEL_DEBUG, json_encode($msg), $classname, $method, $line);
    }
    public static function info($tag, $msg, $classname = '', $method = '', $line = '') {
        if(self::$out === true || (self::$out && in_array(self::$out, array(2, 3, 6, 7, 10, 11, 14, 15)))) self::out($tag, self::DEBUG_LEVEL_INFO, $msg, $classname, $method, $line);
        if(self::$log === true || (self::$log && in_array(self::$log, array(2, 3, 6, 7, 10, 11, 14, 15)))) self::log($tag, self::DEBUG_LEVEL_INFO, $msg, $classname, $method, $line);
    }
    public static function warning($tag, $msg, $classname = '', $method = '', $line = '') {
        if(self::$out === true || (self::$out && in_array(self::$out, array(4, 5, 6, 7, 12, 13, 14, 15)))) self::out($tag, self::DEBUG_LEVEL_WARN, $msg, $classname, $method, $line);
        if(self::$log === true || (self::$log && in_array(self::$log, array(4, 5, 6, 7, 12, 13, 14, 15)))) self::log($tag, self::DEBUG_LEVEL_WARN, $msg, $classname, $method, $line);
    }
    public static function error($tag, $msg, $classname = '', $method = '', $line = '') {
        if(self::$out === true || (self::$out && in_array(self::$out, array(8, 9, 10, 11, 12, 13, 14, 15)))) self::out($tag, self::DEBUG_LEVEL_ERROR, $msg, $classname, $method, $line);
        if(self::$log === true || (self::$log && in_array(self::$log, array(8, 9, 10, 11, 12, 13, 14, 15)))) self::log($tag, self::DEBUG_LEVEL_ERROR, $msg, $classname, $method, $line);
    }
    
    public static function log($tag = '', $lv = 1, $msg = '', $classname = '', $method = '', $line = '') {
        if(!$method) $method = $classname;
        switch($lv) {
            case self::DEBUG_LEVEL_DEBUG:
                $lv = 'DEBUG';
                break;
            case self::DEBUG_LEVEL_INFO:
                $lv = 'INFO';
                break;
            case self::DEBUG_LEVEL_WARN:
                $lv = 'WARN';
                break;
            case self::DEBUG_LEVEL_ERROR:
                $lv = 'ERROR';
                break;
            default:
                $lv = 'DEBUG';
                break;
        }
        $ip = self::ip();
        echo 'uiop[as';self::out($tag, $lv, $msg, $classname, $method, $line);
        syslog(LOG_INFO, date('Y-m-d H:i:s').'.'.floor(microtime()*1000)." $ip LAYWORK [$lv] [$tag] $method:$line $msg");
    }
    public static function out($tag = '', $lv = 1, $msg = '', $classname = '', $method = '', $line = '') {
        if(!$method) $method = $classname;
        switch($lv) {
            case self::DEBUG_LEVEL_DEBUG:
                $lv = 'DEBUG';
                break;
            case self::DEBUG_LEVEL_INFO:
                $lv = 'INFO';
                break;
            case self::DEBUG_LEVEL_WARN:
                $lv = 'WARN';
                break;
            case self::DEBUG_LEVEL_ERROR:
                $lv = 'ERROR';
                break;
            default:
                $lv = 'DEBUG';
                break;
        }
        $ip = self::ip();
        echo '<pre style="padding:0px;margin:0px;border:0px;">';
        echo date('Y-m-d H:i:s').'.'.floor(microtime()*1000)." $ip [$lv] [$tag] $method:$line $msg\r\n";
        echo '</pre>';
    }
    public static function pre($tag = '', $lv = 0x1000, $msg = '', $classname = '', $method = '', $line = '') {
        if(!$method) $method = $classname;
        switch($lv) {
            case self::DEBUG_LEVEL_DEBUG:
                $lv = 'DEBUG';
                break;
            case self::DEBUG_LEVEL_INFO:
                $lv = 'INFO';
                break;
            case self::DEBUG_LEVEL_WARN:
                $lv = 'WARN';
                break;
            case self::DEBUG_LEVEL_ERROR:
                $lv = 'ERROR';
                break;
            default:
                $lv = 'DEBUG';
                break;
        }
        $ip = self::ip();
        echo '<pre style="padding:0px;margin:0px;border:0px;">';
        echo date('Y-m-d H:i:s').'.'.floor(microtime()*1000)." $ip [$lv] [$tag] $method:$line\r\n";
        print_r($msg);
        echo '</pre>';
    }
    public static function ip() {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
    }
}

class_alias('Debugger', 'D');//Layload class alias

class AutoloadException extends Exception {}
?>