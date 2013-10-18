<?php
/**
 * Laywork主类
 * @see https://github.com/lay595715148/laywork3
 * 
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
if(!defined('INIT_LAYWORK')) { exit; }

global $_LAYWORKPATH,$_ROOTPATH;

$_LAYWORKPATH = str_replace("\\", "/", dirname(__DIR__));//Returns parent directory's path
$_ROOTPATH = str_replace("\\", "/", dirname(dirname(__DIR__)));

/**
 * <p>Laywork主类</p>
 * $_LAYWORKPATH:%LAYWORK_PATH%,Laywork里的所有类文件的路径都是相对于此。
 * $_ROOTPATH:%LAYWORK_PATH%的父目录路径，所有配置文件的路径是相对于此。
 * 
 * @author Lay Li
 */
final class Laywork {
    
    /**
     * @staticvar configuration
     */
    public static $configuration = array(
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
        'Criteria' => '/cn/laysoft/laywork/core-PHP5.2/Criteria.class.php',
        'Criterion' => '/cn/laysoft/laywork/core-PHP5.2/Criterion.class.php',
        'TableBean' => '/cn/laysoft/laywork/core-PHP5.2/TableBean.class.php',
        'Template' => '/cn/laysoft/laywork/core-PHP5.2/Template.class.php',
        'Preface' => '/cn/laysoft/laywork/core-PHP5.2/Preface.class.php',
        'Parser' => '/cn/laysoft/laywork/core-PHP5.2/Parser.class.php',
        'DemoAction' => '/cn/laysoft/laywork/demo-PHP5.2/DemoAction.class.php',
        'DemoBean' => '/cn/laysoft/laywork/demo-PHP5.2/DemoBean.class.php',
        'DemoService' => '/cn/laysoft/laywork/demo-PHP5.2/DemoService.class.php',
        'DemoStore' => '/cn/laysoft/laywork/demo-PHP5.2/DemoStore.class.php',
        'DemoTemplate' => '/cn/laysoft/laywork/demo-PHP5.2/DemoTemplate.class.php',
        'DemoPreface' => '/cn/laysoft/laywork/demo-PHP5.2/DemoPreface.class.php',
        
        'Util' => '/cn/laysoft/laywork/util-PHP5.2/Util.class.php'
    );
    /**
     * set laywork path
     * @param $layworkpath laywork directory path,default is empty
     * @return void
     */
    public static function rootpath($rootpath = '') {
        global $_ROOTPATH;
        if(is_dir($rootpath)) {
            $_ROOTPATH = str_replace("\\", "/", $rootpath);
        } else {
            //TODO warning given path isnot a real path
        }
    }
    /**
     * set laywork root path
     * @param $layworkpath laywork directory path,default is empty
     * @return void
     */
    public static function layworkpath($layworkpath = '') {
        global $_LAYWORKPATH;
        if(is_dir($layworkpath)) {
            $_LAYWORKPATH = str_replace("\\", "/", $layworkpath);
        } else {
            //TODO warning given path isnot a real path
        }
    }
    /**
     * initialize autoload function
     * @return void
     */
    public static function initialize($debug = '') {
        spl_autoload_register('Laywork::autoload');
        if($debug) Debugger::initialize($debug);
        Debugger::info('initilize application', 'APPLICATION', __LINE__, __METHOD__, __CLASS__);
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
        $suffixes = array('.php', '.class.php', '.inc');

        if(array_key_exists($classname, $classes)) {//全名映射
            if(is_file($classes[$classname])) {
                require_once $classes[$classname];
                Debugger::info($classes[$classname], 'REQUIRE_ONCE', __LINE__, __METHOD__, __CLASS__);
            } else if(is_file($_CLASSPATH.$classes[$classname])) {
                require_once $_CLASSPATH.$classes[$classname];
                Debugger::info($_CLASSPATH.$classes[$classname], 'REQUIRE_ONCE', __LINE__, __METHOD__, __CLASS__);
            } else {
                //TODO mapping is error
                Debugger::warn('Not found class mapping file by name:'.$classname, 'CLASS_AUTOLOAD', __LINE__, __METHOD__, __CLASS__);
            }
        } else {
            $tmparr = explode("\\", $classname);
            if(count($tmparr) > 1) {//if is namespace
                $name = array_pop($tmparr);
                $path = $_CLASSPATH.'/'.implode('/', $tmparr);
                $required = false;
                //命名空间文件夹查找
                if(is_dir($path)) {
                    $tmppath = $path.'/'.$name;
                    foreach($suffixes as $i=>$suffix) {
                        if(is_file($tmppath.$suffix)) {
                            Debugger::info($tmppath.$suffix, 'REQUIRE_ONCE', __LINE__, __METHOD__, __CLASS__);
                            require_once $tmppath.$suffix;
                            $required = true;
                            break;
                        }
                    }
                    if(!class_exists($classname) && !interface_exists($classname)) {
                        Debugger::warn('Not found by namespace', 'CLASS_AUTOLOAD', __LINE__, __METHOD__, __CLASS__);
                    }
                } else {
                    //TODO not found by namespace dir
                    Debugger::warn('Not found by namespace dir', 'CLASS_AUTOLOAD', __LINE__, __METHOD__, __CLASS__);
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
                                Debugger::info($tmppath.$suffix, 'REQUIRE_ONCE', __LINE__, __METHOD__, __CLASS__);
                                require_once $tmppath.$suffix;
                                break 2;
                            }
                        }
                        continue;
                    } else if($index == count($matches[1]) - 1) {
                        foreach($suffixes as $i=>$suffix) {
                            if(is_file($path.$suffix)) {
                                Debugger::info($path.$suffix, 'REQUIRE_ONCE', __LINE__, __METHOD__, __CLASS__);
                                require_once $path.$suffix;
                                break 2;
                            }
                        }
                        break;
                    } else {
                        //TODO not found by regular match
                        Debugger::warn('Not found by regular match', 'CLASS_AUTOLOAD', __LINE__, __METHOD__, __CLASS__);
                    }
                }
            }
        }
        if(!class_exists($classname) && !interface_exists($classname)) {
            //throw new AutoloadException('class:'.$classname.' autoload error');
            //TODO warning no class mapping by Laywork class autoload function
        }
    }
    /**
     * get configuration by key string
     * @param $keystr key string, example: 'action.index'
     * @param $default if nothing by $keystr,the default value
     * @return mixed
     */
    public static function get($keystr = '', $default = null) {
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
                    Debugger::warn('$configuration[...]["'.$key.'"] has been configured in "'.$keystr.'"', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
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
        $actions = self::get('actions');
        
        if(is_array($actions) && array_key_exists($name, $actions)) {
            //TODO warning has been configured by this name
            Debugger::warn('$configuration["actions"]["'.$name.'"] has been configured', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
        } else if(is_string($name) || is_numeric($name)) {
            Debugger::info('configure action:'.$name.'', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
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
        $services = self::get('services');
        
        if(is_array($services) && array_key_exists($name, $services)) {
            //TODO warning has been configured by this name
            Debugger::warn('$configuration["services"]["'.$name.'"] has been configured', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
        } else {
            Debugger::info('configure service:'.$name.'', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
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
        $stores = self::get('stores');
        
        if(is_array($stores) && array_key_exists($name, $stores)) {
            //TODO warning has been configured by this name
            Debugger::warn('$configuration["stores"]["'.$name.'"] has been configured', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
        } else {
            Debugger::info('configure store:'.$name.'', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
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
        $beans = self::get('beans');
        $config = is_array($config)?$config:array();
        
        if(is_array($beans) && array_key_exists($name, $beans)) {
            //TODO warning has been configured by this name
            Debugger::warn('$configuration["beans"]["'.$name.'"] has been configured', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
        } else {
            Debugger::info('configure bean:'.$name.'', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
            self::set('beans.'.$name, $config);
            //TODO configure a bean
        }
    }
    /**
     * configure a preface
     * @param $name
     * @param $config
     * @return void
     */
    public static function preface($name, $config) {
        $prefaces = self::get('prefaces');
        $config = is_array($config)?$config:array();
        
        if(is_array($prefaces) && array_key_exists($name, $prefaces)) {
            //TODO warning has preface configured by this name
            Debugger::warn('$configuration["prefaces"]["'.$name.'"] has been configured', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
        } else {
            Debugger::info('configure preface:'.$name.'', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
            self::set('prefaces.'.$name, $config);
            //TODO configure a preface
        }
    }
    /**
     * configure a bean
     * @param $name
     * @param $config
     * @return void
     */
    public static function template($name, $config) {
        $templates = self::get('templates');
        $config = is_array($config)?$config:array();
        
        if(is_array($templates) && array_key_exists($name, $templates)) {
            //TODO warning has template configured by this name
            Debugger::warn('$configuration["templates"]["'.$name.'"] has been configured', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
        } else {
            Debugger::info('configure template:'.$name.'', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
            self::set('templates.'.$name, $config);
            //TODO configure a template
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
     * @param $name
     * @return array
     */
    public static function beanConfig($name) {
        return self::get('beans.'.$name);
    }
    /**
     * get preface configuration by name
     * @param $name
     * @return array
     */
    public static function prefaceConfig($name) {
        return self::get('prefaces.'.$name);
    }
    /**
     * get template configuration by name
     * @param $name
     * @return array
     */
    public static function templateConfig($name) {
        return self::get('templates.'.$name);
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
            //Debugger::info('$configuration:'.json_encode($configuration), 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
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
                                Debugger::warn('$configuration["actions"] is not an array', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
                            }
                            break;
                        case 'services':
                            if(is_array($item)) {
                                foreach($item as $name=>$conf) {
                                    self::service($name, $conf);
                                }
                            } else {
                                //TODO warning services is not an array
                                Debugger::warn('$configuration["services"] is not an array', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
                            }
                            break;
                        case 'stores':
                            if(is_array($item)) {
                                foreach($item as $name=>$conf) {
                                    self::store($name, $conf);
                                }
                            } else {
                                //TODO warning stores is not an array
                                Debugger::warn('$configuration["stores"] is not an array', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
                            }
                            break;
                        case 'beans':
                            if(is_array($item)) {
                                foreach($item as $name=>$conf) {
                                    self::bean($name, $conf);
                                }
                            } else {
                                //TODO warning beans is not an array
                                Debugger::warn('$configuration["beans"] is not an array', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
                            }
                            break;
                        case 'prefaces':
                            if(is_array($item)) {
                                foreach($item as $name=>$conf) {
                                    self::preface($name, $conf);
                                }
                            } else {
                                //TODO warning beans is not an array
                                Debugger::warn('$configuration["prefaces"] is not an array', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
                            }
                            break;
                        case 'templates':
                            if(is_array($item)) {
                                foreach($item as $name=>$conf) {
                                    self::template($name, $conf);
                                }
                            } else {
                                //TODO warning beans is not an array
                                Debugger::warn('$configuration["templates"] is not an array', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
                            }
                            break;
                        case 'files':
                            if(is_array($item)) {
                                foreach($item as $file) {
                                    self::configure($file);
                                }
                            } else if(is_string($item)) {
                                self::configure($item);
                            } else {
                                //TODO warning files is not an array or string
                                Debugger::warn('$configuration["files"] is not an array', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
                            }
                            break;
                        case 'debug':
                            //update Debugger
                            Debugger::initialize($item);
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
        } else if(is_string($configuration)) {
            Debugger::info('configure file:'.$configuration, 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
            if(is_file($configuration)) {
                $tmparr = include_once $configuration;
            } else if(is_file($_ROOTPATH.$configuration)) {
                $tmparr = include_once $_ROOTPATH.$configuration;
            } else {
                Debugger::warn($configuration.' is not a real file', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
                $tmparr = array();
            }
            
            if(empty($tmparr)) {
                self::configure($tmparr);
            } else {
                self::configure($tmparr, false);
            }
        } else {
            //TODO warning unkown configuration type
            Debugger::warn('unkown configuration type', 'CONFIGURE', __LINE__, __METHOD__, __CLASS__);
        }
    }
    /**
     * start laywork
     * @param $action action name,default is empty
     * @param $method method name,default is empty
     * @param $params param array,default is empty
     */
    public static function start($action = 'index', $method = '', $params = '') {
        Debugger::info('start application', 'APPLICATION', __LINE__, __METHOD__, __CLASS__);
        try {
            self::getInstance()->run($action, $method, $params);
        } catch (Exception $e) {
            Debugger::error($e->getMessage()."\r\n".$e->getTraceAsString(), 'Exception', __LINE__, __METHOD__, __CLASS__);
        }
        Debugger::info('finish application', 'APPLICATION', __LINE__, __METHOD__, __CLASS__);
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
        //global $_LOADPATH, $_CLASSPATH, $_ROOTPATH, $_LAYWORKPATH;        
        
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

if(!class_exists('W', false)) {
    class_alias('Laywork', 'W');//Layload class alias
}

class AutoloadException extends Exception {}
?>