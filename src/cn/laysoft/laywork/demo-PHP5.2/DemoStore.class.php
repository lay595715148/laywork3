<?php
if(!defined('INIT_LAYWORK')) { exit; }

class DemoStore extends Store {
    public function __call($method, $arguments) {
        if(!method_exists($this, $method)) {
            echo "Using DemoStore,please check your action-service-store configuration\n<br>";
        }
    }
}
?>