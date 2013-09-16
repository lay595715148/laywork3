<?php
/**
 * SQL条件部分子句组成结构对象类
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * SQL条件部分子句组成结构对象类
 * @author liaiyong
 */
class Condition extends Base {
    /**
     * @var int 当前存放Cell对象的起始序号
     */
    private $index = 0;
    /**
     * @var array<Cell> 存放Cell对象的数组
     */
    private $conds = array();
    /**
     * @var int|bool 元素间连接是否使用OR(默认AND),或使用OR的起始位置
     */
    private $orpos = false;
    /**
     * 构造方法
     */
    public function __construct() {
    }
    /**
     * 获取已经存放的Cell对象数组
     * @return array
     */
    public function getConds() {
        return $this->conds;
    }
    /**
     * 设置元素间连接是否使用OR(默认AND),或使用OR的起始位置
     * @param int $orpos
     * @return void
     */
    public function setOrpos($orpos) {
        $this->orpos = 0 + $orpos;
    }
    /**
     * 获取元素间连接是否使用OR(默认AND),或使用OR的起始位置
     * @return int|bool
     */
    public function getOrpos() {
        return $this->orpos;
    }
    /**
     * 将单个Cell对象或Cell对象数组存放进$conds数组中
     * @param Cell|array<Cell> $cell 单个Cell对象或Cell对象数组
     * @return int|bool the index of Cell array before put into
     */
    public function putCell($cell) {
        $conds  = &$this->conds;
        $index  = &$this->index;
        $return = $index;

        if(is_array($cell)) {
            foreach($cell as $c) {
                if(!is_a($c, 'Cell')) continue;
                $conds[$index] = $c;
                $index++;
            }
        } else {
            if(!is_a($cell, 'Cell')) return false;
            $conds[$index] = $cell;
            $index++;
        }

        return $return;
    }
    /**
     * 将$conds数组中指定索引位的Cell对象移除
     * @param $index int the index of cell
     * @return bool
     */
    public function removeCell($index) {
        $conds = &$this->conds;
        if(array_key_exists($index, $conds)) {
            unset($conds[$index]);
            return true;
        } else {
            return false;
        }
    }
    /**
     * no mysql_real_escape_string
     * 
     * contact all the cell to SQL string
     * use 'OR' from index $orpos,if $orpos = true or 0 all the cell will contact with 'OR'
     * @return string SQL clause string
     */
    public function toSQLString() {
        $orpos = &$this->orpos;
        $conds = &$this->conds;
        $sql   = '';
        foreach($conds as $k=>$cell) {
            if($sql === '') {
                $sql .= $cell->toSQLString();
            } else {
                $sql .= ( $orpos === true || is_numeric($orpos) && $k >= $orpos )?' OR ':' AND ';
                $sql .= $cell->toSQLString();
            }
        }
        return $sql;
    }
}
?>
