<?php
/**
 * 统一入口文件
 * @author liaiyong
 */
define('INIT_LAYWORK', true);//标记

ini_set('output_buffering', 'on');
ini_set('implicit_flush', 'off');
//Turn on output buffering
//ob_start();
ob_implicit_flush(false);

//require_once __DIR__.'/lib/PHP-Error/src/php_error.php';\php_error\reportErrors();
require_once __DIR__.'/lib/laybug/laybug.php';
require_once __DIR__.'/src/Laywork.php';
//Layload see https://github.com/lay595715148/layload
require_once __DIR__.'/lib/layload/layload.php';

Laywork::configure(__DIR__.'/inc/config.files.php');
Laywork::initialize();
Layload::loadpath(__DIR__.'/inc');
Layload::classpath(__DIR__.'/inc/classes');
Layload::initialize();

//Laywork::start();
?>
