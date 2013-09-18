<?php
namespace cn\laysoft\laywork\demo;
use cn\laysoft\laywork\core\Action;
use Laywork;
if(!defined('INIT_LAYWORK')) { exit; }

class DemoAction extends Action {
    public function launch() {
        if(Laywork::$debug) { echo '<pre>';print_r(func_get_args());echo '</pre>'; }
        $ret = $this->services['in']->doit();
        $ret = $this->services['out']->doit();
    }
}
?>