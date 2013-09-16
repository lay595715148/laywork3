<?php
/**
 * SQL条件元素工具类
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * SQL条件元素工具类
 * @Version: 0.1.48 (build 130723)
 */
class Cell extends AbstractBean {
    const MODEL_EQUAL = 0;
    const MODEL_UNEQUAL = 1;
    const MODEL_GREATER = 2;
    const MODEL_LESS = 3;
    const MODEL_IN = 4;
    const MODEL_NOTIN = 5;
    const MODEL_LIKE = 6;
    const MODEL_UNLIKE = 7;
    const MODEL_FORMAT_EQUAL = ':=';
    const MODEL_FORMAT_UNEQUAL = ':~';
    const MODEL_FORMAT_GREATER = ':>';
    const MODEL_FORMAT_LESS = ':<';
    const MODEL_FORMAT_IN = ':+';
    const MODEL_FORMAT_NOTIN = ':-';
    const MODEL_FORMAT_LIKE = ':%';
    const MODEL_FORMAT_UNLIKE = ':^';
    const FLAG_CONNECT = '&';
    const FLAG_EXPLODE = ',';
    const TYPE_STRING = 0;
    const TYPE_NUMBER = 1;
    /**
     * 构造方法
     * @param string $name 元素名
     * @param string $value 元素值
     * @param string $mode 元素模型
     * @param string $type 元素值类型
     */
    public function __construct($name = false, $value = false, $mode = false, $type = false) {
        $this->properties = array(
            'name' => ($name === false || !is_string($name))?'name':$name,
            'mode' => ($mode === false || !is_int($mode))?0:$mode,
            'value' => (is_string($value) || is_numeric($value) || is_array($value))?$value:'',//string, number, if mode = 6 or 7 it can be an array
            'type' => ($type === false || !is_int($type))?0:$type,
        );
    }
    /**
     * parse cells from filter string
     * @param string $filter filter string
     */
    public static function parseFilterString($filter = '') {
        $result = false;
        $cells  = array();
        if($filter && is_string($filter)) {
            $filters = explode(Cell::FLAG_CONNECT,$filter);
            foreach($filters as $i => $f) {
                $matches = array();
                $pattern = '/^(.*)(:[=|~|>|<|+|\-|%|\^]{1})(.*)$/';
                $return  = preg_match($pattern,$f,$matches);
                if(!$return) { continue; }
                $cell = new Cell();
                $cell->setName($matches[1]);
                $cell->setValue($matches[3]);
                switch($matches[2]) {
                    case Cell::MODEL_FORMAT_EQUAL:
                        $cell->setMode(Cell::MODEL_EQUAL);
                        break;
                    case Cell::MODEL_FORMAT_UNEQUAL:
                        $cell->setMode(Cell::MODEL_UNEQUAL);
                        break;
                    case Cell::MODEL_FORMAT_GREATER:
                        $cell->setMode(Cell::MODEL_GREATER);
                        break;
                    case Cell::MODEL_FORMAT_LESS:
                        $cell->setMode(Cell::MODEL_LESS);
                        break;
                    case Cell::MODEL_FORMAT_IN:
                        $value = explode(Cell::FLAG_EXPLODE,$matches[3]);
                        $cell->setValue($value);
                        $cell->setMode(Cell::MODEL_IN);
                        break;
                    case Cell::MODEL_FORMAT_NOTIN:
                        $value = explode(Cell::FLAG_EXPLODE,$matches[3]);
                        $cell->setValue($value);
                        $cell->setMode(Cell::MODEL_NOTIN);
                        break;
                    case Cell::MODEL_FORMAT_LIKE:
                        $value = explode(Cell::FLAG_EXPLODE,$matches[3]);
                        $cell->setValue($value);
                        $cell->setMode(Cell::MODEL_LIKE);
                        break;
                    case Cell::MODEL_FORMAT_UNLIKE:
                        $value = explode(Cell::FLAG_EXPLODE,$matches[3]);
                        $cell->setValue($value);
                        $cell->setMode(Cell::MODEL_UNLIKE);
                        break;
                }
                $cells[] = $cell;
            }
        }
        if(count($cells) == 1) {
            $result = $cells[0];
        } else if(count($cells) > 1) {
            $result = $cells;
        }
        return $result;
    }
    /**
     * waiting
     * @param string $sql SQL string
     */
    public static function parseSQLString($sql = '') {
        $cell = false;
        if($sql && is_string($sql)) {
        }
        return $cell;
    }
    /**
     * transfer this to filter string
     */
    public function toFilterString() {
        $filter = false;
        $name   = &$this->getName();
        $type   = &$this->getType();
        $mode   = &$this->getMode();
        $value  = &$this->getValue();
        switch($mode) {
            case Cell::MODEL_EQUAL:
                $filter = $name.Cell::MODEL_FORMAT_EQUAL.$value;
                break;
            case Cell::MODEL_UNEQUAL:
                $filter = $name.Cell::MODEL_FORMAT_UNEQUAL.$value;
                break;
            case Cell::MODEL_GREATER:
                $filter = $name.Cell::MODEL_FORMAT_GREATER.$value;
                break;
            case Cell::MODEL_LESS:
                $filter = $name.Cell::MODEL_FORMAT_LESS.$value;
                break;
            case Cell::MODEL_IN:
                if(is_array($value)) {
                    $filter = $name.Cell::MODEL_FORMAT_IN.implode(Cell::FLAG_EXPLODE, $value);
                } else {
                    $filter = $name.Cell::MODEL_FORMAT_IN.$value;
                }
                break;
            case Cell::MODEL_NOTIN:
                if(is_array($value)) {
                    $filter = $name.Cell::MODEL_FORMAT_NOTIN.implode(Cell::FLAG_EXPLODE, $value);
                } else {
                    $filter = $name.Cell::MODEL_FORMAT_NOTIN.$value;
                }
                break;
            case Cell::MODEL_LIKE:
                if(is_array($value)) {
                    $filter = $name.Cell::MODEL_FORMAT_LIKE.implode(Cell::FLAG_EXPLODE, $value);
                } else {
                    $filter = $name.Cell::MODEL_FORMAT_LIKE.$value;
                }
                break;
            case Cell::MODEL_UNLIKE:
                if(is_array($value)) {
                    $filter = $name.Cell::MODEL_FORMAT_UNLIKE.implode(Cell::FLAG_EXPLODE, $value);
                } else {
                    $filter = $name.Cell::MODEL_FORMAT_UNLIKE.$value;
                }
                break;
            default:
                break;
        }
        return $filter;
    }
    /**
     * transfer this to SQL part 
     */
    public function toSQLString() {
        $sql   = false;
        $name  = &$this->getName();
        $type  = &$this->getType();
        $mode  = &$this->getMode();
        $value = &$this->getValue();

        switch($mode) {
            case Cell::MODEL_EQUAL:
                if(is_array($value) && $type === Cell::TYPE_NUMBER) {
                    $sql = '';
                    foreach($value as $v) {
                        $sql .= ($sql)?"AND `$name` = $v":" ( `$name` = $v ";
                    }
                    $sql .= (count($value))?' ) ':'';
                } else if(is_array($value) && $type != Cell::TYPE_NUMBER){
                    $sql = '';
                    foreach($value as $v) {
                        $sql .= ($sql)?"AND `$name` = '$v'":" ( `$name` = '$v' ";
                    }
                    $sql .= (count($value))?' ) ':'';
                } else if(!is_array($value) && $type === Cell::TYPE_NUMBER){
                    $sql = " ( `$name` = $value ) ";
                } else if(!is_array($value) && $type != Cell::TYPE_NUMBER){
                    $sql = " ( `$name` = '$value' ) ";
                }
                break;
            case Cell::MODEL_UNEQUAL:
                if(is_array($value) && $type === Cell::TYPE_NUMBER) {
                    $sql = '';
                    foreach($value as $v) {
                        $sql .= ($sql)?"AND `$name` <> $v":" ( `$name` <> $v ";
                    }
                    $sql .= (count($value))?' ) ':'';
                } else if(is_array($value) && $type != Cell::TYPE_NUMBER){
                    $sql = '';
                    foreach($value as $v) {
                        $sql .= ($sql)?"AND `$name` <> '$v'":" ( `$name` <> '$v' ";
                    }
                    $sql .= (count($value))?' ) ':'';
                } else if(!is_array($value) && $type === Cell::TYPE_NUMBER){
                    $sql = " ( `$name` <> $value ) ";
                } else if(!is_array($value) && $type != Cell::TYPE_NUMBER){
                    $sql = " ( `$name` <> '$value' ) ";
                }
                break;
            case Cell::MODEL_GREATER:
                if(is_array($value) && $type === Cell::TYPE_NUMBER) {
                    $sql = '';
                    foreach($value as $v) {
                        $sql .= ($sql)?"AND `$name` > $v":" ( `$name` > $v ";
                    }
                    $sql .= (count($value))?' ) ':'';
                } else if(is_array($value) && $type != Cell::TYPE_NUMBER){
                    $sql = '';
                    foreach($value as $v) {
                        $sql .= ($sql)?"AND `$name` > '$v'":" ( `$name` > '$v' ";
                    }
                    $sql .= (count($value))?' ) ':'';
                } else if(!is_array($value) && $type === Cell::TYPE_NUMBER){
                    $sql = " ( `$name` > $value ) ";
                } else if(!is_array($value) && $type != Cell::TYPE_NUMBER){
                    $sql = " ( `$name` > '$value' ) ";
                }
                break;
            case Cell::MODEL_LESS:
                if(is_array($value) && $type === Cell::TYPE_NUMBER) {
                    $sql = '';
                    foreach($value as $v) {
                        $sql .= ($sql)?"AND `$name` < $v":" ( `$name` < $v ";
                    }
                    $sql .= (count($value))?' ) ':'';
                } else if(is_array($value) && $type != Cell::TYPE_NUMBER){
                    $sql = '';
                    foreach($value as $v) {
                        $sql .= ($sql)?"AND `$name` < '$v'":" ( `$name` < '$v' ";
                    }
                    $sql .= (count($value))?' ) ':'';
                } else if(!is_array($value) && $type === Cell::TYPE_NUMBER){
                    $sql = " ( `$name` < $value ) ";
                } else if(!is_array($value) && $type != Cell::TYPE_NUMBER){
                    $sql = " ( `$name` < '$value' ) ";
                }
                break;
            case Cell::MODEL_IN:
                if(is_array($value) && $type === Cell::TYPE_NUMBER) {
                    $sql = " ( `$name` IN ( ".implode(" , ", $value)." ) ) ";
                } else if(is_array($value) && $type != Cell::TYPE_NUMBER){
                    $sql = " ( `$name` IN ( '".implode("' , '", $value)."' ) ) ";
                } else if(!is_array($value) && $type === Cell::TYPE_NUMBER){
                    $sql = " ( `$name` IN ( $value ) ) ";
                } else if(!is_array($value) && $type != Cell::TYPE_NUMBER){
                    $sql = " ( `$name` IN ( '$value' ) ) ";
                }
                break;
            case Cell::MODEL_NOTIN:
                if(is_array($value) && $type === Cell::TYPE_NUMBER) {
                    $sql = " ( `$name` NOT IN ( ".implode(" , ", $value)." ) ) ";
                } else if(is_array($value) && $type != Cell::TYPE_NUMBER){
                    $sql = " ( `$name` NOT IN ( '".implode("' , '", $value)."' ) ) ";
                } else if(!is_array($value) && $type === Cell::TYPE_NUMBER){
                    $sql = " ( `$name` NOT IN ( $value ) ) ";
                } else if(!is_array($value) && $type != Cell::TYPE_NUMBER){
                    $sql = " ( `$name` NOT IN ( '$value' ) ) ";
                }
                break;
            case Cell::MODEL_LIKE:
                if(is_array($value)) {
                    $sql = '';
                    foreach($value as $v) {
                        $sql .= ($sql)?"AND `$name` LIKE '%$v%'":" ( `$name` LIKE '%$v%' ";
                    }
                    $sql .= (count($value))?' ) ':'';
                } else if(!is_array($value)){
                    $sql = " ( `$name` LIKE '%$value%' ) ";
                }
                break;
            case Cell::MODEL_UNLIKE:
                if(is_array($value)) {
                    $sql = '';
                    foreach($value as $v) {
                        $sql .= ($sql)?"AND `$name` NOT LIKE '%$v%' ":" ( `$name` NOT LIKE '%$v%' ";
                    }
                    $sql .= (count($value))?' ) ':'';
                } else if(!is_array($value)){
                    $sql = " ( `$name` LIKE '%$value%' ) ";
                }
                break;
            default:
                break;
        }
        return $sql;
    }
}
?>
