<?php 
$st = date('Y-m-d H:i:s').'.'.floor(microtime()*1000);
define('INIT_LAYWORK', true);//标记
include_once __DIR__.'/bootstrap.php';

Laywork::configure('/laywork/example/inc/config.files.php');//-PHP5.2
Laywork::initialize();
/*
print_r(json_encode(Laywork::$configuration));echo '<br>';
*/
Laywork::start('in', false, array('a'=>'b'));
$et = date('Y-m-d H:i:s').'.'.floor(microtime()*1000);
Debugger::debug(array($st, $et));
//print_r(get_included_files());echo '<br>';
?>
