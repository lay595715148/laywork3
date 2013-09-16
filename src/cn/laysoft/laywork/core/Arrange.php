<?php
/**
 * SQL排序工具类
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * SQL排序工具类
 * @Version: 0.1.48 (build 130723)
 */
class Arrange extends Base {
    /**
     * @var int $index current position
     */
    private $index = 0;
    /**
     * @var array $index order field array
     */
    private $order = array();
    /**
     * get order array
     * @return array
     */
    public function getOrder() {
        return $this->order;
    }
    /**
     * put an order element
     * @param string $field table field string
     * @param bool $desc bool of desc
     * @return int position before put
     */
    public function putOrder($field, $desc = false) {
        $order = &$this->order;
        $index = &$this->index;
        $return = $index;

        if(is_array($field)) {
            foreach($field as $f) {
                $order[$index] = array('field'=>$f, 'desc'=>$desc);
                $index++;
            }
        } else {
            $order[$index] = array('field'=>$field, 'desc'=>$desc);
            $index++;
        }
        return $return;
    }
    /**
     * remove an order element
     * @param int $index position index
     * @return bool
     */
    public function removeOrder($index) {
        $order = &$this->order;
        if(array_key_exists($index, $order)) {
            unset($order[$index]);
            return true;
        } else {
            return false;
        }
    }
    /**
     * convert into SQL string clause
     * @return string
     */
    public function toSQLString() {
        $order = &$this->order;
        $sql   = '';
        foreach($order as $k=>$ord) {
            if($sql === '') {
                $sql .= '`'.$ord['field'].(($ord['desc'])?'` DESC ':'` ASC ');
            } else {
                $sql .= ',';
                $sql .= '`'.$ord['field'].(($ord['desc'])?'` DESC ':'` ASC ');
            }
        }
        return $sql;
    }
}
?>
