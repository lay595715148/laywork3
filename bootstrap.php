<?php
/**
 * 统一入口文件
 * @author liaiyong
 */
if(!defined('INIT_LAYWORK')) { exit; }

//Turn on output buffering
ob_start();
ini_set('output_buffering', 'on');
ini_set('implicit_flush', 'off');

require_once __DIR__.'/lib/layload/layload.php';

Layload::rootpath(__DIR__);
Layload::classpath(__DIR__.'/src');
Layload::configure('/inc/classes.laywork.php');
Layload::initialize(false);

Laywork::initialize(false);

class M extends cn\laysoft\laywork\core\TableBean {
}
//print_r(spl_autoload_functions());
?>
