<?php
namespace cn\laysoft\laywork\demo;
use cn\laysoft\laywork\core\Template;
use Laywork,Debugger;
if(!defined('INIT_LAYWORK')) { exit; }

class DemoTemplate extends Template {
    public function initialize() {
        Debugger::info('doit', 'DemoTemplate', __LINE__, __METHOD__, __CLASS__);
        parent::initialize();
        //Debugger::debug('DemoTemplate', 'Yes, It\'s Template!', __LINE__, __METHOD__, __CLASS__);
    }
}
?>