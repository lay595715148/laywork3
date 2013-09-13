<?php
define('INIT_LAYWORK', true);//标记
include_once __DIR__.'/bootstrap.php';
//Layload::rootpath(dirname(__DIR__));
//Layload::classpath(dirname(__DIR__));

Laywork::configure('/laywork/example/inc/config.files.php');
/*
print_r(json_encode(Laywork::$configuration));echo '<br>';

class M extends cn\laysoft\laywork\core\TableBean {
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
$m = new M();
echo '<pre>';print_r($m->rowsToArray(array(array('id'=>'21.22sd', 'datetime'=>'2011-10-31 03:16:40', 'type'=>'2'), array('id'=>'12sd', 'name'=>123, 'datetime'=>1320002200))));echo '</pre>';*/
//print_r(spl_autoload_functions());
Laywork::start();
?>
