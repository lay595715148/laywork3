<?php
namespace cn\laysoft\laywork\demo;
use cn\laysoft\laywork\core\Action;
if(!defined('INIT_LAYWORK')) { exit; }

class DemoAction extends Action {
    public static $action = array(1, 1, 1);
    public function launch() {
        echo '33333';
    }
}
?>