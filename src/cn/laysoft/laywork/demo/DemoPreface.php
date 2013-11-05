<?php
namespace cn\laysoft\laywork\demo;
use cn\laysoft\laywork\core\Preface;
use Laywork,Debugger;
if(!defined('INIT_LAYWORK')) { exit; }

class DemoPreface extends Preface {
    public function initialize() {
        Debugger::info('initialize', 'DemoPreface');
        parent::initialize();
        //Debugger::debug('DemoPreface', 'Yes, It\'s Preface!');
    }
}
?>