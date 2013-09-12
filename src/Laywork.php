<?php
if(!defined('INIT_LAYWORK')) { exit; }

global $_LAYWORKPATH;

$_LAYWORKPATH = str_replace("\\", "/", dirname(__DIR__));//Returns parent directory's path

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
    public static $configuration = array();
    public static function layworkpath($layworkpath) {
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
        $suffixes = array('.php','.class.php','.inc');

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
    /**
     * laywork configuration
     * @param $configuration a file or file array or config array
     * @param $isFile sign file,default is true
     * @return void
     */
    public static function configure($configuration, $isFile = true) {
        
    }
    /**
     * start laywork
     * @param $action action name,default is empty
     * @param $method method name,default is empty
     * @param $params param array,default is empty
     */
    public static function start($action = '', $method = '', $params = '') {
        global $_ROOTPATH,$_CLASSPATH,$_LAYWORKPATH;
        echo '$_ROOTPATH: '.$_ROOTPATH.'<br>';
        echo '$_CLASSPATH: '.$_CLASSPATH.'<br>';
        echo '$_LAYWORKPATH: '.$_LAYWORKPATH.'<br>';
    }
}

?>