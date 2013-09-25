<?php
namespace cn\laysoft\laywork\demo;
use cn\laysoft\laywork\core\Action;
use Laywork,Debugger,Exception;
if(!defined('INIT_LAYWORK')) { exit; }

class DemoAction extends Action {
    public function launch() {
        Debugger::info('DemoAction', 'launch', __CLASS__, __METHOD__, __LINE__);
        Debugger::debug('Debugger', spl_autoload_functions(), __CLASS__, __METHOD__, __LINE__);
        //$ret = $this->services['in']->doit();
        //throw new Exception('test exception');
        Debugger::debug('Debugger', array('debug'=>Laywork::get('debug')), __CLASS__, __METHOD__, __LINE__);
        new \MysqlServer();
        $ret = $this->services['out']->doit();
    }
}
?>