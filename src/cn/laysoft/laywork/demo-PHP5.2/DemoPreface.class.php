<?php
if(!defined('INIT_LAYWORK')) { exit; }

class DemoPreface extends Preface {
    public function initialize() {
        Debugger::info('initialize', 'DemoPreface');
        parent::initialize();
        //Debugger::debug('DemoPreface', 'Yes, It\'s Preface!');
    }
}
?>