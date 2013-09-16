<?php
namespace cn\laysoft\laywork\demo;
use cn\laysoft\laywork\core\TableBean;
if(!defined('INIT_LAYWORK')) { exit; }

class DemoBean extends TableBean {
    public function __construct() {
        parent::__construct(array(
            'id' => 0,
            'name' => '',
            'datetime' => '',
            'type' => 1
        ),array(
            'id' => 'integer',
            'name' => 'string',
            'datetime' => array('dateformat'=>'Y-m-d H:i'),
            'type' => array(1, 2, 3, 4)
        ));
    }
    public function table() {
        return '`laysoft`.`lay_demo`';
    }
    public function columns() {
        return array(
            'id' => 'integer',
            'name' => 'string',
            'datetime' => 'datetime',
            'type' => 'integer'
        );
    }
    public function mapping() {
        return array(
            'id' => 'id',
            'name' => 'name',
            'datetime' => 'datetime',
            'type' => 'type'
        );
    }
    public function pk() {
        return 'id';
    }
    public function toInsertFields() {
        $mapping = $this->mapping();
        $fields  = array();
        foreach($this->toProperties() as $name) {
            if($name == $this->pk()) continue;
            if($name == 'datetime') continue;
            
            $fields[] = array_key_exists($name, $mapping)?$mapping[$name]:$name;
        }
        return $fields;
    }
    public function otherFormat($value, $propertype) {
        if(is_numeric($value)) {
            return intval($value);
        } else if(is_string($value)) {
            return strtotime($value);
        }
    }
}
?>