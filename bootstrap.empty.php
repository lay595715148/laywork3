<?php
/**
 * 统一入口文件
 * @author liaiyong
 */
define('INIT_LAYWORK', true);//标记

//Turn on output buffering
ob_start();
ini_set('output_buffering', 'on');
ini_set('implicit_flush', 'off');

//require_once __DIR__.'/lib/PHP-Error/src/php_error.php';\php_error\reportErrors();
require_once __DIR__.'/lib/laybug/laybug.php';
require_once __DIR__.'/src/Laywork.php';
//Layload see https://github.com/lay595715148/layload
require_once __DIR__.'/lib/layload/layload.php';

/**
 * TODO
 * Laywork::configure();
 * Laywork::initialize();
 * Layload::loadpath(__DIR__);
 * Layload::classpath(__DIR__);
 * Layload::configure();
 * Layload::initialize();
 * 
 * Laywork::start();
 */
?>
