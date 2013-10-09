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

//require_once __DIR__.'/lib/PHP-Error/src/php_error.php';\php_error\reportErrors();

require_once __DIR__.'/src/Laywork.php';
Laywork::initialize();

//Layload see https://github.com/lay595715148/layload
require_once __DIR__.'/lib/layload/layload.php';
Layload::loadpath(__DIR__);
Layload::classpath(__DIR__.'/example');
Layload::initialize('true');
?>
