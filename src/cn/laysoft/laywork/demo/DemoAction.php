<?php
namespace cn\laysoft\laywork\demo;
use cn\laysoft\laywork\core\Action;
use Laywork,Debugger,Exception;
if(!defined('INIT_LAYWORK')) { exit; }

class DemoAction extends Action {
    public function launch() {
        Debugger::debug('DemoAction', spl_autoload_functions());
        //$ret = $this->services['in']->doit();
        throw new Exception('test exception');
        $ret = $this->services['out']->doit();
    }
}
?>