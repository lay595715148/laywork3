<?php
/**
 * 数据库pdo访问类
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
use Laywork;
use PDO;
use PDOStatement;
use PDOException;
use Exception;
if(!defined('INIT_LAYWORK')) { exit; }

class PdoStore extends Mysql {
    /**
     * @var PDO 数据库PDO连接
     */
    private $link;
    /**
     * @var PDOStatement 数据库PDO操作结果
     */
    private $result;
    
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
        if(isset($this->config['lazy']) && !$this->config['lazy']) {
            $this->connect();
        }
        return $this;
    }
    /**
     * 打开数据库PDO连接
     */
    public function connect() {
        $config = &$this->config;
        $link   = &$this->link;

        $driver = isset($config['driver'])?$config['driver']:'mysql';
        $host = $config['host'];
        $user = isset($config['username'])?$config['username']:$config['user'];
        $password = $config['password'];
        $database = isset($config['database'])?$config['database']:$config['schema'];
        $options = is_array($config['options'])?$config['options']:array();
        $dsn = "$driver:dbname=$database;host=$host";

        $link = new PDO($dsn, $user, $password, $options);
    }
    
    /**
     * 关闭数据库PDO连接
     * @return bool
     */
    public function close() {
        $this->link = null;
        return true;
    }
    
    /**
     * 执行sql查询语句
     * @param string $sql
     * @param string $encoding
     * @param bool $showSQL
     * @return mixed
     */
    public function query($sql, $encoding = '', $showSQL = false) {
        $config = &$this->config;
        $result = &$this->result;
        $link   = &$this->link;
        if(!$link) { $this->connect(); }
        //释放到数据库服务的连接，以便发出其他 SQL 语句，但使语句处于一个可以被再次执行的状态。见：http://cn2.php.net/manual/zh/pdostatement.closecursor.php
        if($result instanceof PDOStatement) { $result->closeCursor(); }
        
        if($encoding) {
            $link->query('SET NAMES '.$encoding, $link);
        } else if($config['encoding']) {
            $encoding = &$config['encoding'];
            $link->query('SET NAMES '.$encoding, $link);
        }
        if($showSQL) {
            echo '<pre>'.$sql.'</pre>';
        } else if($config['showsql']) {
            $encoding = &$config['showsql'];
            echo '<pre>'.$sql.'</pre>';
        }
        if($sql) {
            $result = $link->query($sql);
            //echo '<pre>';print_r($result);echo '</pre>';
        }

        return $result;
    }
    /**
     * 执行sql语句
     * @param string $sql
     * @param string $encoding
     * @param bool $showSQL
     * @return mixed
     */
    public function exec($sql, $encoding = '', $showSQL = false) {
        $config = &$this->config;
        $result = &$this->result;
        $link   = &$this->link;
        if(!$link) { $this->connect(); }
        //释放到数据库服务的连接，以便发出其他 SQL 语句，但使语句处于一个可以被再次执行的状态。见：http://cn2.php.net/manual/zh/pdostatement.closecursor.php
        if($result instanceof PDOStatement) { $result->closeCursor(); }
        
        if($encoding) {
            $link->query('SET NAMES '.$encoding, $link);
        } else if($config['encoding']) {
            $encoding = &$config['encoding'];
            $link->query('SET NAMES '.$encoding, $link);
        }
        if($showSQL) {
            echo '<pre>'.$sql.'</pre>';
        } else if($config['showsql']) {
            $encoding = &$config['showsql'];
            echo '<pre>'.$sql.'</pre>';
        }
        if($sql) {
            $result = $link->exec($sql);
            //echo '<pre>';print_r($result);echo '</pre>';
        }

        return $result;
    }
    /**
     * PDO执行insert语句
     * @param TableBean $table 表名
     * @param array|string $fields 表字段数组
     * @param array|string $values 表字段对应值数组
     * @param bool $replace
     * @param bool $returnid
     * @return int|bool
     */
    public function insert($table, $fields = '', $values = '', $replace = false, $returnid = true) {
        $link = &$this->link;
        $result = &$this->result;

        $sql = $this->insertSQL($table, $fields, $values, $replace);
        $result = $this->exec($sql);

        return ($returnid)?$link->lastInsertId():$result;
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
        $result = $this->exec($sql);

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
        $result = $this->exec($sql);

        return $result;
    }
    
    /**
     * 将结果集转换为指定数量的数组，并释放
     * @param int $count
     * @param mixed $result
     * @return array
     */
    public function toArray($count = 0, $result = '') {
        $rows = array();
        $result = ($result)?$result:$this->result;
        if(!$result || !($result instanceof PDOStatement)) {
            //TODO result is empty or null or not an instance of PDOStatement
        } else {
            if($count != 0) {
                $i = 0;
                if($result->rowCount()) {
                    while($i < $count && $row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $rows[$i] = (array)$row;
                        $i++;
                    }
                }
            } else {
                if($result->rowCount()) {
                    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                }
            }
            //释放到数据库服务的连接，以便发出其他 SQL 语句，但使语句处于一个可以被再次执行的状态。见：http://cn2.php.net/manual/zh/pdostatement.closecursor.php
            if($result instanceof PDOStatement) { $result->closeCursor(); }
        }
        
        return $rows;
    }
}
?>