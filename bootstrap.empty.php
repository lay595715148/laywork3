<?php
/**
 * 统一入口文件
 * @author liaiyong
 */
define('INIT_LAYWORK', true);//标记

//require_once __DIR__.'/lib/PHP-Error/src/php_error.php';\php_error\reportErrors();
require_once __DIR__.'/lib/index.php';
require_once __DIR__.'/src/Laywork.php';

/**
 * TODO
 * Layload::loadpath(__DIR__);
 * Layload::classpath(__DIR__);
 * Layload::configure();
 * Laywork::configure();
 * 
 * Layload::initialize();
 * Laywork::initialize();
 * 
 * Laywork::start();
 */
?>
