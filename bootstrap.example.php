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

require_once __DIR__.'/lib/PHP-Error/src/php_error.php';\php_error\reportErrors();
require_once __DIR__.'/lib/index.php';
require_once __DIR__.'/src/Laywork.php';

Layload::loadpath(__DIR__.'/example');
Layload::classpath(__DIR__.'/example/classes');
Laywork::configure(__DIR__.'/example/include/config.files.php');

Layload::initialize();
Laywork::initialize();

//Laywork::start();
?>
