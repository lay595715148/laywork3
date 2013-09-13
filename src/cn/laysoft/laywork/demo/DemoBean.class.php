<?php
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
        return 'lay_m';
    }
    public function columns() {
        return array(
            'id' => 'integer',
            'name' => 'string',
            'datetime' => 'datetime'
        );
    }
    public function mapping() {
        return array(
            'id' => 'id',
            'name' => 'name',
            'datetime' => 'datetime'
        );
    }
    public function pk() {
        return 'id';
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