<?php 
$st = date('Y-m-d H:i:s').'.'.floor(microtime()*1000);
include_once __DIR__.'/bootstrap.php';
/*
print_r(json_encode(Laywork::$configuration));echo '<br>';
*/
Laywork::start();
$et = date('Y-m-d H:i:s').'.'.floor(microtime() * 1000);
Debugger::debug(array($st, $et));
//print_r(get_included_files());echo '<br>';
?>
