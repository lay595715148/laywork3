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

Layload::initialize(true);
Layload::loadpath(__DIR__);
Layload::classpath(__DIR__.'/../example');
Layload::configure('/inc/classes.laywork.php');

require_once __DIR__.'/src/Laywork.php';
Laywork::initialize(array(true, Debugger::DEBUG_LEVEL_WARN + Debugger::DEBUG_LEVEL_ERROR));
Laywork::rootpath(dirname(__DIR__));
?>
