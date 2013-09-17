<?php
if(!defined('INIT_LAYWORK')) { exit; }

class DemoAction extends Action {
    public function launch() {
        //echo '33333';
        $ret = $this->services['in']->doit();
        $ret = $this->services['out']->doit();
    }
}
?>