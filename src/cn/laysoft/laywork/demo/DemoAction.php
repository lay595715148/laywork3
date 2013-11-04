<?php
namespace cn\laysoft\laywork\demo;
use cn\laysoft\laywork\core\Action;
use Laywork,Debugger,Exception;
if(!defined('INIT_LAYWORK')) { exit; }

class DemoAction extends Action {
    public function launch() {
        Debugger::info('launch', 'DemoAction', __LINE__, __METHOD__, __CLASS__);
        extract(pathinfo($_SERVER['PHP_SELF']));
        $extension = isset($extension)?$extension:'';
        switch($extension) {
            case 'json':
            case 'xml':
                $this->template->push('hello-world', '!');
                break;
            default:
                echo 'Hello World!';
        }
    }
}
?>