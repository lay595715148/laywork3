<?php
if(!defined('INIT_LAYWORK')) { exit; }

class DemoPreface extends Preface {
    public function initialize() {
        Debugger::info('initialize', 'DemoPreface', __LINE__, __METHOD__, __CLASS__);
        parent::initialize();
        //Debugger::debug('DemoPreface', 'Yes, It\'s Preface!', __LINE__, __METHOD__, __CLASS__);
    }
}
?>