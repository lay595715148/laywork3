<?php
if(!defined('INIT_LAYWORK')) { exit; }

class DemoAction extends Action {
    public function launch() {
        Debugger::info('launch', 'DemoAction');
        /*$funs = spl_autoload_functions();
        Debugger::debug($funs, 'Debugger');
        Debugger::debug(end($funs), 'Debugger');
        $funs = spl_autoload_functions();
        Debugger::debug($funs, 'Debugger');*/
        //$ret = $this->services['in']->doit();
        //throw new Exception('test exception');
        //Debugger::debug(array('debug'=>Laywork::get('debug')), 'Debugger');
        //new \MysqlServer();
        //$ret = $this->service('out')->doit();
        echo 'Hello World!';
    }
}
?>