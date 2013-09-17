<?php
/**
 * 与数据库关联的数据模型基础类
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * 与数据库关联的数据模型基础类
 * @abstract
 */
abstract class TableBean extends Bean {
    /**
     * return the object relational mapping array
     * @return array
     */
    public abstract function mapping();
    /**
     * return the table name
     * @return string
     */
    public abstract function table();
    /**
     * return the primary key
     * @return string
     */
    public abstract function pk();
    /**
     * return all columns
     * @return array
     */
    public abstract function columns();
    /**
     * return the basic columns
     * @return array
     */
    public function basicColumns() {
        $columns = $this->columns();
        return array_diff_key($columns, array($this->pk() => 'integer'));
    }
    /**
     * 
     * @return array
     */
    public function otherColumns() {
        return $this->columns();
    }
    /**
     * 得到对应表名
     */
    public function toTable() {
        return $this->table();
    }
    /**
     * 得到对应属性名
     * @param string $field field name string
     * @return string
     */
    public function toProperty($field) {
        $mapping  = $this->mapping();
        $property = array_search($field, $mapping);
        return ($property)?$property:$field;
    }
    /**
     * 得到所有属性名
     * @return array
     */
    public function toProperties() {
        return array_keys($this->toArray());
    }
    /**
     * 得到对应表字段名
     * @param string $property class property name string
     * @return string
     */
    public function toField($property) {
        $mapping = $this->mapping();
        return array_key_exists($property, $mapping)?$mapping[$property]:$property;
    }
    /**
     * 得到所有表字段名
     * @return array
     */
    public function toFields() {
        $mapping = $this->mapping();
        $fields = array();
        foreach($this->toArray() as $name=>$v) {
            $fields[] = array_key_exists($name, $mapping)?$mapping[$name]:$name;
        }
        return $fields;
    }
    /**
     * 得到用于插入数据的所有表字段名
     * @return array
     */
    public function toInsertFields() {
        $mapping = $this->mapping();
        $fields  = array();
        foreach($this->toProperties() as $name) {
            if($name == $this->pk()) continue;
            
            $fields[] = array_key_exists($name, $mapping)?$mapping[$name]:$name;
        }
        return $fields;
    }
    /**
     * 得到所有表字段的值
     * @return array
     */
    public function toValues() {
        $mapping = $this->mapping();
        $values  = array();
        foreach($this->toProperties() as $name) {
            $field          = array_key_exists($name, $mapping)?$mapping[$name]:$name;
            $values[$field] = $v;
        }
        return $values;
    }
    /**
     * 将从数据库得到的结果数组转换为数据模型实体数组
     * @param array $rows database result row array
     * @return array
     */
    public function rowsToEntities($rows) {
        $entities  = array();
        $className = get_class($this);
        if(is_array($rows) && !empty($rows)) {
            foreach($rows as $k=>$row) {
                if(is_array($row)) {
                    $bean       = new $className();
                    $return     = $bean->rowToEntity($row);
                    $entities[] = $bean;
                }
            }
            return $entities;
        } else {
            return $entities;
        }
    }
    /**
     * 将从数据库得到的结果数组转换一个数据模型实体
     * @param array $row database result row
     * @return TableBean
     */
    public function rowToEntity($row) {
        $mapping = $this->mapping();
        if(is_array($row)) {
            foreach($this->toProperties() as $name) {
                $key         = array_key_exists($name, $mapping)?$mapping[$name]:$name;
                $this->$name = array_key_exists($key, $row)?$row[$key]:'';
            }
            return $this;
        } else {
            return $this;
        }
    }
    /**
     * 将从数据库得到的结果数组转换为以ID为索引的数据模型的二维数组
     * @param array $rows database result row array
     * @return TableBean
     */
    public function rowsToArrayID($rows){
        $arrs      = array();
        $className = get_class($this);
        if(is_array($rows)) {
            foreach($rows as $k=>$row) {
                if(is_array($row) && class_exists($className)) {
                    $bean   = new $className();
                    $arr    = $bean->rowToArray($row);
                    $arrs[$arr['id']] = $arr;
                }
            }
            return $arrs;
        } else {
            return;
        }
    }
    /**
     * 将从数据库得到的结果数组转换数据模型二维数组
     * @param array $rows database result row array
     * @return TableBean
     */
    public function rowsToArray($rows) {
        $arrs      = array();
        $className = get_class($this);
        if(is_array($rows)) {
            foreach($rows as $k=>$row) {
                if(is_array($row)) {
                    $bean   = new $className();
                    $arr    = $bean->rowToArray($row);
                    $arrs[] = $arr;
                }
            }
            return $arrs;
        } else {
            return $arrs;
        }
    }
    /**
     * 将从数据库得到的结果数组转换一个数据模型数组
     * @param array $row database result row
     * @return TableBean
     */
    public function rowToArray($row) {
        $arr = array();
        if(is_array($row)) {
            $bean = $this->rowToEntity($row);
            $arr  = $bean->toArray();
            return $arr;
        } else {
            return $arr;
        }
    }
}

/**
 * 数据模型与表映射异常
 * @version: 0.0.1 (build 130911)
 */
class TableMappingException extends Exception {}
?>