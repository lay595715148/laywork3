<?php
namespace cn\laysoft\laywork\demo;
use cn\laysoft\laywork\core\Action;
use Laywork,Debugger,Exception;
if(!defined('INIT_LAYWORK')) { exit; }

class DemoAction extends Action {
    public function launch() {
        Debugger::info('DemoAction', 'launch', __LINE__, __METHOD__, __CLASS__);
        /*$funs = spl_autoload_functions();
        Debugger::debug('Debugger', $funs, __LINE__, __METHOD__, __CLASS__);
        Debugger::debug('Debugger', end($funs), __LINE__, __METHOD__, __CLASS__);
        $funs = spl_autoload_functions();
        Debugger::debug('Debugger', $funs, __LINE__, __METHOD__, __CLASS__);*/
        //$ret = $this->services['in']->doit();
        //throw new Exception('test exception');
        //Debugger::debug('Debugger', array('debug'=>Laywork::get('debug')), __LINE__, __METHOD__, __CLASS__);
        //new \MysqlServer();
        $ret = $this->services['out']->doit();
    }
}
?>