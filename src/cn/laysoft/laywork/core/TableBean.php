<?php
/**
 * 与数据库关联的数据模型基础类
 * @author liaiyong<595715148@qq.com>
 * @Version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * 与数据库关联的数据模型基础类
 * @Version: 0.0.1 (build 130723)
 */
abstract class TableBean extends Bean {
    /**
     * 得到对应表名
     */
    public function toTable() {
        global $_CFG;
        $className = get_class($this);
        $tables    = &$_CFG['mapping']['tables'];
        if(!array_key_exists($className,$_CFG['mapping']['tables'])) throw new TableMappingException('There\'s no table mapping in $_CFG[\'mapping\'][\'tables\']');
        return $tables[$className];
    }
    /**
     * 得到对应属性名
     * @param string $field field name string
     * @return string
     */
    public function toProperty($field) {
        global $_CFG;
        $className = get_class($this);
        if(!array_key_exists($className,$_CFG['mapping'])) throw new TableMappingException('There\'s no table fields mapping in $_CFG[\'mapping\']');
        $mapping   = &$_CFG['mapping'][$className];
        $property  = array_search($field,$mapping);
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
        global $_CFG;
        $className = get_class($this);
        if(!array_key_exists($className,$_CFG['mapping'])) throw new TableMappingException('There\'s no table fields mapping in $_CFG[\'mapping\']');
        $mapping   = &$_CFG['mapping'][$className];
        $field     = (is_array($mapping) && array_key_exists($property,$mapping))?$mapping[$property]:$property;
        return $field;
    }
    /**
     * 得到所有表字段名
     * @return array
     */
    public function toFields() {
        global $_CFG;
        $className = get_class($this);
        if(!array_key_exists($className,$_CFG['mapping'])) throw new TableMappingException('There\'s no table fields mapping in $_CFG[\'mapping\']');
        $mapping   = &$_CFG['mapping'][$className];
        $fields    = array();
        if($mapping && is_array($mapping)) {
            foreach($this->toArray() as $k=>$v) {
                $name     = $k;
                $fields[] = array_key_exists($name,$mapping)?$mapping[$name]:$name;
            }
            return $fields;
        } else {
            return;
        }
    }
    /**
     * 得到用于插入数据的所有表字段名
     * @return array
     */
    public function toInsertFields() {
        global $_CFG;
        $className = get_class($this);
        if(!array_key_exists($className,$_CFG['mapping'])) throw new TableMappingException('There\'s no table fields mapping in $_CFG[\'mapping\']');
        $mapping   = &$_CFG['mapping'][$className];
        $fields    = array();
        if($mapping && is_array($mapping)) {
            foreach($this->toArray() as $k=>$v) {
                if($k === 'id') continue;
                $name     = $k;
                $fields[] = array_key_exists($name,$mapping)?$mapping[$name]:$name;
            }
            return $fields;
        } else {
            return;
        }
    }
    /**
     * 得到所有表字段的值
     * @return array
     */
    public function toValues() {
        global $_CFG;
        $className = get_class($this);
        if(!array_key_exists($className,$_CFG['mapping'])) throw new TableMappingException('There\'s no table fields mapping in $_CFG[\'mapping\']');
        $mapping   = &$_CFG['mapping'][$className];
        $values    = array();
        if($mapping) {
            foreach($this->toArray() as $k=>$v) {
                $name           = $k;
                $field          = array_key_exists($name,$mapping)?$mapping[$name]:$name;
                $values[$field] = $v;
            }
            return $values;
        } else {
            return;
        }
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
                if(is_array($row) && class_exists($className)) {
                    $bean       = new $className();
                    $return     = $bean->rowToEntity($row);
                    $entities[] = $bean;
                }
            }
            return $entities;
        } else {
            return;
        }
    }
    /**
     * 将从数据库得到的结果数组转换一个数据模型实体
     * @param array $row database result row
     * @return TableBean
     */
    public function rowToEntity($row) {
        global $_CFG;
        $className = get_class($this);
        $mapping   = &$_CFG['mapping'][$className];
        if(!array_key_exists($className,$_CFG['mapping'])) throw new TableMappingException('There\'s no table fields mapping in $_CFG[\'mapping\']');
        if(is_array($row) && $mapping) {
            foreach($this->toArray() as $k=>$v) {
                    $name     = $k;
                    $key      = array_key_exists($name,$mapping)?$mapping[$name]:$name;
                    $this->$k = array_key_exists($key,$row)?$row[$key]:'';
            }
            return $this;
        } else {
            return;
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
                if(is_array($row) && class_exists($className)) {
                    $bean   = new $className();
                    $arr    = $bean->rowToArray($row);
                    $arrs[] = $arr;
                }
            }
            return $arrs;
        } else {
            return;
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
            return;
        }
    }
}

/**
 * 数据模型与表映射异常
 * @Version: 0.0.1 (build 130911)
 */
class TableMappingException extends \Exception {}
?>