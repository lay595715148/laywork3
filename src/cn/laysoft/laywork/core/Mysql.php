<?php
/**
 * mysql数据库访问类
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
use Laywork,Debugger;
use Exception;
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * mysql数据库访问类
 * @author Lay Li
 */
class Mysql extends Store {
    /**
     * @var mixed mysql数据库连接
     */
    protected $link;
    /**
     * @var mixed mysql数据库操作结果
     */
    protected $result;
    /**
     * 析构
     */
    public function __destruct() {
        if($this->link) mysql_close($this->link);
    }
    /**
     * 初始化
     */
    public function initialize() {
        //默认懒连接
        if(isset($this->config['lazy']) && $this->config['lazy'] === true) {
            $this->connect();
        }
        return parent::initialize();
    }
    /**
     * 打开mysql数据库连接
     */
    public function connect() {
        Debugger::info('connect', 'Mysql', __LINE__, __METHOD__, __CLASS__);
        
        $config = &$this->config;
        $link   = &$this->link;

        $host = $config['host'];
        $user = isset($config['username'])?$config['username']:$config['user'];
        $password = $config['password'];
        $database = isset($config['database'])?$config['database']:$config['schema'];
        $forcenew = isset($config['forcenew'])?$config['forcenew']:false;

        if($link = @mysql_connect($host, $user, $password, $forcenew)) {
            if($database && !@mysql_select_db($database, $link)) {
                throw new MysqlException('Cannot select mysql database:'.$database.'!');
            } else {
                //TODO no database selected
            }
        } else {
            throw new MysqlException('Cannot connect to mysql server:'.$host.'!');
        }
    }
    /**
     * 关闭mysql数据库连接
     * @return bool
     */
    public function close() {
        $link = &$this->link;
        return @mysql_close($link);
    }
    /**
     * mysql执行sql语句
     * @param string $sql
     * @param string $encoding
     * @param bool $showSQL
     * @return mixed
     */
    public function query($sql, $encoding = '', $showSQL = false) {
        Debugger::info('query', 'Mysql', __LINE__, __METHOD__, __CLASS__);
        $config = &$this->config;
        $result = &$this->result;
        $link   = &$this->link;
        if(!$link) { $this->connect(); }
        
        if($encoding) {
            mysql_query('SET NAMES '.$encoding, $link);
        } else if($config['encoding']) {
            $encoding = &$config['encoding'];
            mysql_query('SET NAMES '.$encoding, $link);
        }
        if($showSQL) {
            //echo '<pre>'.$sql.'</pre>';
            Debugger::info('showsql:'.$sql, 'Mysql', __LINE__, __METHOD__, __CLASS__);
        } else if($config['showsql']) {
            $encoding = &$config['showsql'];
            //echo '<pre>'.$sql.'</pre>';
            Debugger::info('showsql:'.$sql, 'Mysql', __LINE__, __METHOD__, __CLASS__);
        }
        if($sql) {
            $result = mysql_query($sql, $link);
        }

        return $result;
    }
    /**
     * mysql执行insert语句
     * @param TableBean $table 表名
     * @param array|string $fields 表字段数组
     * @param array|string $values 表字段对应值数组
     * @param bool $replace
     * @param bool $returnid
     * @return int|bool
     */
    public function insert($table, $fields = '', $values = '', $replace = false, $returnid = true) {
        $link   = &$this->link;
        $result = &$this->result;

        $sql = $this->insertSQL($table, $fields, $values, $replace);
        Debugger::info('insert', 'Mysql', __LINE__, __METHOD__, __CLASS__);
        $result = $this->query($sql);

        return ($returnid)?mysql_insert_id($link):$result;
    }
    /**
     * mysql执行delete语句
     * @param TableBean $table 表名
     * @param array|string|Condition $condition 条件
     * @return bool
     */
    public function delete($table, $condition = '') {
        $result = &$this->result;

        $sql = $this->deleteSQL($table, $condition);
        Debugger::info('delete', 'Mysql', __LINE__, __METHOD__, __CLASS__);
        $result = $this->query($sql);

        return $result;
    }
    /**
     * mysql执行update语句
     * @param TableBean $table 表名
     * @param array|string $fields 表字段数组
     * @param array|string $values 表字段对应值数组
     * @param array|string|Condition $condition 条件
     * @return bool
     */
    public function update($table, $fields = '', $values = '', $condition = '') {
        $result = &$this->result;

        $sql = $this->updateSQL($table, $fields, $values, $condition);
        Debugger::info('update', 'Mysql', __LINE__, __METHOD__, __CLASS__);
        $result = $this->query($sql);

        return $result;
    }
    /**
     * mysql执行select语句
     * @param TableBean $table 表名
     * @param array|string $fields 表字段数组
     * @param array|string|Condition $condition 条件
     * @param array|string $group 
     * @param array|string|Arrange $order
     * @param string $limit 
     * @return mixed
     */
    public function select($table, $fields = '', $condition = '', $group = '', $order = '', $limit = '') {//$group is not useful
        $result = &$this->result;
        
        $sql = $this->selectSQL($table, $fields, $condition, $group, $order, $limit);
        Debugger::info('select', 'Mysql', __LINE__, __METHOD__, __CLASS__);
        $result = $this->query($sql);

        return $result;
    }
    /**
     * mysql执行记数语句
     * @param TableBean $table 表名
     * @param array|string|Condition $condition 条件
     * @param array|string $group 
     * @return bool
     */
    public function count($table, $condition = '', $group = '') {
        $result = &$this->result;
        
        $sql = $this->countSQL($table, $condition, $group);
        $result = $this->query($sql);
        
        return $result;
    }
    /**
     * 转换为insert语句
     * @param TableBean $table 表名
     * @param array|string $fields 表字段数组
     * @param array|string $values 表字段对应值数组
     * @param bool $replace
     * @return string
     */
    public function insertSQL($table, $fields = '', $values = '', $replace = false) {
        if(!$table) { $table = $this->bean; }
        if(!($table instanceof TableBean)) { return false; }

        $tablename = $table->table();
        if(is_array($values)) {
            $values = $this->array2Value($fields, $values, $table);
        } else if(!is_string($values)) {
            return false;
        }
        if(is_array($fields)) {
            $fields = $this->array2Field($fields, $table);
        } else if(!is_string($fields)) {
            return false;
        }
        
        $sql   = (($replace)?'REPLACE':'INSERT')." INTO $tablename ( $fields ) VALUES ( $values )";
        return $sql;
    }
    /**
     * 转换为delete语句
     * @param TableBean $table 表名
     * @param array|string|Condition $condition 条件
     * @return string
     */
    public function deleteSQL($table, $condition = '') {
        if(!$table) { $table = $this->bean; }
        if(!($table instanceof TableBean)) { return false; }

        $tablename = $table->table();
        if(is_array($condition)) {
            $condition = $this->array2Where($condition, $table);
        } else if(is_a($condition,'Condition')) {
            $condition = $this->condition2Where($condition, $table);
        } else if(!is_string($condition)) {
            return false;
        }
        
        $sql    = "DELETE FROM $tablename $condition";
        return $sql;
    }
    /**
     * 转换为update语句
     * @param TableBean $table 表名
     * @param array|string $fields 表字段数组
     * @param array|string $values 表字段对应值数组
     * @param array|string|Condition $condition 条件
     * @return string
     */
    public function updateSQL($table, $fields = '', $values = '', $condition = '') {
        if(!$table) { $table = $this->bean; }
        if(!($table instanceof TableBean)) { return false; }

        $tablename = $table->table();
        if(is_array($values) && is_array($fields)) {
            $values = $this->array2Setter($fields,$values,$table);
        } else if(is_array($values)){
            $values = $this->array2Setter('',$values,$table);
        } else if(!is_string($values)) {
            return false;
        }
        if(is_array($condition)) {
            $condition = $this->array2Where($condition,$table);
        } else if(is_a($condition,'Condition')) {
            $condition = $this->condition2Where($condition,$table);
        } else if(!is_string($condition)) {
            return false;
        }
        
        $sql    = "UPDATE $tablename SET $values $condition";
        return $sql;
    }
    /**
     * 转换为select语句
     * @param TableBean $table 表名
     * @param array|string $fields 表字段数组
     * @param array|string|Condition $condition 条件
     * @param array|string $group 
     * @param array|string|Arrange $order
     * @param string $limit 
     * @return string
     */
    public function selectSQL($table, $fields = '', $condition = '', $group = '', $order = '', $limit = '') {//$group is not useful
        if(!$table) { $table = $this->bean; }
        if(!($table instanceof TableBean)) { return false; }

        $tablename = $table->table();
        if(is_array($fields)) {
            $fields = $this->array2Field($fields,$table);
        } else if($fields && !is_string($fields)) {
            return false;
        } else if(is_string($fields) && trim($fields)) {
            //
        } else {
            $fields = '*';
        }
        if(is_array($condition)) {
            $condition = $this->array2Where($condition,$table);
        } else if(is_a($condition,'Condition')) {
            $condition = $this->condition2Where($condition,$table);
        } else if(!is_string($condition)) {
            return false;
        }
        if(!is_string($group)) {
            $group = "";
        }
        if(is_a($order,'Arrange')) {
            $order = $this->arrange2Order($order,$table);
        } else if(is_array($order)) {
            $order = $this->array2Order($order,$table);
        } else if(!is_string($order)) {
            $order = "";
        }
        if(!is_string($limit)) {
            $limit = "";
        }

        $sql    = "SELECT $fields FROM $tablename $condition $group $order $limit";
        return $sql;
    }
    /**
     * 转换为记数语句
     * @param TableBean $table 表名
     * @param array|string|Condition $condition 条件
     * @param array|string $group 
     * @return string
     */
    public function countSQL($table, $condition = '', $group = '') {
        if(!$table) { $table = $this->bean; }
        if(!($table instanceof TableBean)) { return false; }

        $tablename = $table->table();
        if(is_array($condition)) {
            $condition = $this->array2Where($condition,$table);
        } else if(is_a($condition,'Condition')) {
            $condition = $this->condition2Where($condition,$table);
        } else if(!is_string($condition)) {
            return false;
        }
        if(!is_string($group)) {
            $group = "";
        }
        
        $sql    = "SELECT count(*) as count FROM $tablename $condition $group";
        return $sql;
    }
    /**
     * 将结果集转换为指定数量的数组
     * @param int $count
     * @param mixed $result
     * @return array
     */
    public function toArray($count = 0, $result = '') {
        $rows = array();
        $result = ($result)?$result:$this->result;
        if(!$result) {
            //TODO result is empty or null
        } else if($count != 0) {
            $i = 0;
            if(@mysql_num_rows($result)) {
                while($i < $count && $row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                    $rows[$i] = (array)$row;
                    $i++;
                }
            }
        } else {
            $i = 0;
            if(@mysql_num_rows($result)) {
                while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
                    $rows[$i] = (array)$row;
                    $i++;
                }
            }
        }
        
        return $rows;
    }
    /**
     * 获取结果集
     * @return mixed
     */
    public function toResult() {
        return $this->result;
    }
    /**
     * 获取结果集中的行数或执行影响的行数
     * @param mixed $result
     * @param bool $isselect
     * @return mixed
     */
    public function toCount($result = '', $isselect = true) {
        if(!$result) { $result = &$this->result; }
        if($isselect) {
            return mysql_num_rows($result);
        } else {
            return mysql_affected_rows($this->link);
        }
    }

    /**
     * get the class name by table name from the class-table-mapping
     * @param TableBean $table
     * @return string
     */
    protected function getClassByTable($table) {
        return get_class($table);
    }
    /**
     * get field SQL by an array and table name from the class-table-field-mapping
     * @param array $arr
     * @param string $table
     * @return string
     */
    protected function array2Field($arr, $table) {
        $str     = '';
        $count   = 0;
        $mapping = $table->mapping();

        foreach($arr as $v) {
            if(array_search($v, $mapping) && $count > 0) {
                $str .= ",`$v`";
                $count++;
            } else if(array_search($v, $mapping) && $count == 0) {
                $str .= "`$v`";
                $count++;
            } else if(array_key_exists($v, $mapping) && $count > 0) {
                $str .= ",`".$mapping[$v]."`";
                $count++;
            } else if(array_key_exists($v, $mapping) && $count == 0) {
                $str .= "`".$mapping[$v]."`";
                $count++;
            }
        }

        return $str;
    }
    /**
     * get value SQL by a field array,a value array and table name from the class-table-field-mapping
     * @param array $fields
     * @param array $values
     * @param string $table
     * @return string
     */
    protected function array2Value($fields, $values, $table) {
        $str     = '';
        $count   = 0;
        $mapping = $table->mapping();

        if(is_array($fields) && is_array($values)) {
            foreach($fields as $k) {
                $v = $values[$k];
                $v = mysql_escape_string($v);
                if((array_search($k, $mapping) || array_key_exists($k, $mapping)) && $count > 0) {
                    $str .= ",'$v'";
                    $count++;
                } else if((array_search($k, $mapping) || array_key_exists($k, $mapping)) && $count == 0){
                    $str .= "'$v'";
                    $count++;
                }
            }
        }

        return $str;
    }
    /**
     * get setter SQL by a field array,a value array and table name from the class-table-field-mapping
     * @param array $fields
     * @param array $values
     * @param array $table
     * @return string
     */
    protected function array2Setter($fields, $values, $table) {
        $str     = '';
        $count   = 0;
        $mapping = $table->mapping();
    
        if($fields && is_array($fields)) {
            foreach($fields as $k) {
                $v = $values[$k];
                $v = mysql_escape_string($v);
                if(array_search($k, $mapping) && $count > 0) {
                    $str .= ",`$k` = '$v'";
                    $count++;
                } else if(array_search($k, $mapping) && $count == 0){
                    $str .= "`$k` = '$v'";
                    $count++;
                } else if(array_key_exists($k, $mapping) && $count > 0) {
                    $str .= ",`".$mapping[$k]."` = '$v'";
                    $count++;
                } else if(array_key_exists($k, $mapping) && $count == 0){
                    $str .= "`".$mapping[$k]."` = '$v'";
                    $count++;
                }
            }
        } else {
            foreach($values as $k=>$v) {
                $v = mysql_escape_string($v);
                if(!is_numeric($k) && array_search($k, $mapping) && $count > 0) {
                    $str .= ",`$k` = '$v'";
                    $count++;
                } else if(!is_numeric($k) && array_search($k, $mapping) && $count == 0){
                    $str .= "`$k` = '$v'";
                    $count++;
                } else if(!is_numeric($k) && array_key_exists($k, $mapping) && $count > 0) {
                    $str .= ",`".$mapping[$k]."` = '$v'";
                    $count++;
                } else if(!is_numeric($k) && array_key_exists($k, $mapping) && $count == 0){
                    $str .= "`".$mapping[$k]."` = '$v'";
                    $count++;
                }
            }
        }

        return $str;
    }
    /**
     * get where SQL by an array and table name from the class-table-field-mapping
     * @param array $arr
     * @param string $table
     * @return string
     */
    protected function array2Where($arr, $table) {
        $str     = '';
        $count   = 0;
        $mapping = $table->mapping();

        foreach($arr as $k=>$v) {
            if(is_a($v,'Condition') && $count > 0) {
                $str .= ' AND ('.substr($this->condition2Where($v, $table),6).')';
                $count++;
                continue;
            } else if(is_a($v,'Condition') && $count == 0){
                $str .= '('.substr($this->condition2Where($v, $table),6).')';
                $count++;
                continue;
            }
            
            $v = mysql_escape_string($v);
            if(!is_numeric($k) && array_search($k, $mapping) && $count > 0) {
                $str .= " AND `$k` = '$v'";
                $count++;
            } else if(!is_numeric($k) && array_search($k, $mapping) && $count == 0){
                $str .= "`$k` = '$v'";
                $count++;
            } else if(!is_numeric($k) && array_key_exists($k, $mapping) && $count > 0){
                $str .= " AND `".$mapping[$k]."` = '$v'";
                $count++;
            } else if(!is_numeric($k) && array_key_exists($k, $mapping) && $count == 0) {
                $str .= "`".$mapping[$k]."` = '$v'";
                $count++;
            }
        }

        return ($str)?"WHERE $str":"WHERE 0";
    }
    /**
     * analyze an instance of Condition to where sql part
     * @param Condition $obj
     * @param string $table
     * @return string
     */
    protected function condition2Where($obj, $table) {
        $str       = '';
        $mapping = $table->mapping();
        $orpos   = $obj->getOrpos();
        $conds   = $obj->getConds();//clone

        foreach($conds as $k=>$cell) {
            $field = $cell->getName();
            $value = $cell->getValue();
            if(is_array($value)) {
                foreach($value as $_k=>$v) {
                    $value[$_k] = mysql_escape_string($v);
                }
            } else {
                $value = mysql_escape_string($value);
            }
            $cell->setValue($value);

            if($str === '' && array_search($field, $mapping)) {
                $str .= $cell->toSQLString();
            } else if($str === '' && array_key_exists($field, $mapping)) {
                $cell->setName($mapping[$field]);
                $str .= $cell->toSQLString();
            } else if(array_search($field, $mapping)) {
                $str .= ( $orpos === true || is_numeric($orpos) && $k >= $orpos )?' OR ':' AND ';
                $str .= $cell->toSQLString();
            } else if(array_key_exists($field, $mapping)) {
                $cell->setName($mapping[$field]);
                $str .= ( $orpos === true || is_numeric($orpos) && $k >= $orpos )?' OR ':' AND ';
                $str .= $cell->toSQLString();
            }
        }
        return ($str)?"WHERE $str":"WHERE 0";
    }
    /**
     * convert array to ORDER SQL clause
     * array('my_field'=>'DESC','your_field'=>'ASC')
     * array('my_field'=>1,'your_field'=>0)
     * @param array $arr
     * @param string $table
     * @return string
     */
    protected function array2Order($arr, $table) {
        $str     = '';
        $mapping = $table->mapping();
        foreach($arr as $k=>$or) {
            if(is_numeric($k)) {
                $field = $or;
                $desc  = false;
            } else if(is_string($k)){
                $field = $k;
                $desc  = ($or == 'DESC' || $or)?true:false;
            }
            if($str === '' && array_search($field, $mapping)) {
                $str  .= '`'.$field.(($desc)?'` DESC ':'` ASC ');
            } else if($str === '' && array_key_exists($field, $mapping)) {
                $field = $mapping[$field];
                $str  .= '`'.$field.(($desc)?'` DESC ':'` ASC ');
            } else if(array_search($field, $mapping)) {
                $str  .= ', ';
                $str  .= '`'.$field.(($desc)?'` DESC ':'` ASC ');
            } else if(array_key_exists($field, $mapping)) {
                $field = $mapping[$field];
                $str  .= ', ';
                $str  .= '`'.$field.(($desc)?'` DESC ':'` ASC ');
            }
        }
        return ($str)?"ORDER BY $str":"";
    }
    /**
     * analyze an instance of Arrange to order sql clause
     * @param Arrange $obj
     * @param string $table
     * @return string
     */
    protected function arrange2Order($obj, $table) {
        $str     = '';
        $mapping = $table->mapping();
        $order   = $obj->getOrder();

        foreach($order as $k=>$ord) {
            $field = $ord['field'];
            $desc  = $ord['desc'];
            if($str === '' && array_search($field, $mapping)) {
                $str  .= '`'.$field.(($desc)?'` DESC ':'` ASC ');
            } else if($str === '' && array_key_exists($field, $mapping)) {
                $field = $mapping[$field];
                $str  .= '`'.$field.(($desc)?'` DESC ':'` ASC ');
            } else if(array_search($field, $mapping)) {
                $str  .= ', ';
                $str  .= '`'.$field.(($desc)?'` DESC ':'` ASC ');
            } else if(array_key_exists($field, $mapping)) {
                $field = $mapping[$field];
                $str  .= ', ';
                $str  .= '`'.$field.(($desc)?'` DESC ':'` ASC ');
            }
        }
        return ($str)?"ORDER BY $str":"";
    }
}

/**
 * mysql操作异常
 * @author liaiyong
 */
class MysqlException extends Exception {}
?>