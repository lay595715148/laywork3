<?php
if(!defined('INIT_LAYWORK')) { exit; }

global $_LAYWORKPATH;

$_LAYWORKPATH = str_replace("\\", "/", __DIR__.'/..');

class Laywork extends cn\laysoft\laywork\core\Base {
    public static function layworkpath($layworkpath) {
        global $_LAYWORKPATH;
        $_LAYWORKPATH = str_replace("\\", "/", is_dir($layworkpath)?$layworkpath:(__DIR__.'/..'));
    }
    public static function start($action = '', $method = '', $params = '') {
        echo '11';
    }
}
?>