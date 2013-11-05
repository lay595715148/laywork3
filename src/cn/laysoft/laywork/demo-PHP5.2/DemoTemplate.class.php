<?php
if(!defined('INIT_LAYWORK')) { exit; }

class DemoTemplate extends Template {
    public function initialize() {
        Debugger::info('doit', 'DemoTemplate');
        parent::initialize();
        //Debugger::debug('DemoTemplate', 'Yes, It\'s Template!');
    }
}
?>