<?php
define('INIT_LAYWORK', true);//标记
include_once __DIR__.'/bootstrap.php';
//Layload::rootpath(dirname(__DIR__));
//Layload::classpath(dirname(__DIR__));

Laywork::configure('/laywork/example/inc/config.files.php');
/*
print_r(json_encode(Laywork::$configuration));echo '<br>';
*/
Laywork::start();
//print_r(get_included_files());echo '<br>';
?>
