<?php
/**
 * 统一入口文件
 * @author liaiyong
 */
if(!defined('INIT_LAYWORK')) { exit; }

//Turn on output buffering
ob_start();
ini_set('output_buffering', 'on');
ini_set('implicit_flush', 'off');

require_once __DIR__.'/lib/layload/layload.php';

Layload::rootpath(__DIR__);
Layload::classpath(__DIR__.'/src');
Layload::configure('/inc/classes.laywork.php');
Layload::initialize(false);

Laywork::initialize(false);

class M extends cn\laysoft\laywork\core\TableBean {
    public function __construct() {
        parent::__construct(array(
            'id' => 0,
            'name' => '',
            'datetime' => ''
        ),array(
            'id' => 'integer',
            'name' => 'string',
            'datetime' => array('other'=>'time')
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
//echo '<pre>';print_r($m->rowsToArray(array(array('id'=>'21.22sd', 'datetime'=>'2011-10-31 03:16:40'), array('id'=>'12sd', 'name'=>123, 'datetime'=>1320002200))));echo '</pre>';
//print_r(spl_autoload_functions());
?>
