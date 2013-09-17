<?php
namespace cn\laysoft\laywork\demo;
use cn\laysoft\laywork\core\Action;
if(!defined('INIT_LAYWORK')) { exit; }

class DemoAction extends Action {
    public function launch() {
        //echo '33333';
        $ret = $this->services['in']->doit();
        $ret = $this->services['out']->doit();
    }
}
?>