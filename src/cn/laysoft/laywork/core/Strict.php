<?php
namespace cn\laysoft\laywork\core;
use Exception;
if(!defined('INIT_LAYWORK')) { exit; }

class Strict extends Base {
    /**
     * magic setter
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value) {
        if(!property_exists($this, $name)) {
            throw new PropertyNotFoundException('There is no property:'.$name.' in class:'.get_class($this));
        }
    }
    /**
     * magic getter
     *
     * @param string $name
     * @return void
     */
    public function &__get($name) {
        if(!property_exists($this, $name)) {
            throw new PropertyNotFoundException('There is no property:'.$name.' in class:'.get_class($this));
        }
    }
}

/**
 * Property not found exception
 */
class PropertyNotFoundException extends Exception {}
?>
