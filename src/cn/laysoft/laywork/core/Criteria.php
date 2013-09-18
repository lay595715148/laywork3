<?php
/**
 * SQL条件部分子句构建类
 * @see https://github.com/lay595715148/laywork3
 * 
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
use Laywork;
use Exception;
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * SQL条件部分子句构建类
 * @author Lay Li
 */
class Criteria extends Bean {
    const COMBINE_AND = 1;
    const COMBINE_OR = 2;
    const COMBINE_BRACKET_AND = 3;
    const COMBINE_BRACKET_OR = 4;
    const COMBINE_NOT_AND = 5;
    const COMBINE_NOT_OR = 6;
    const COMBINE_BRACKET_NOT_AND = 7;
    const COMBINE_BRACKET_NOT_OR = 8;
    /**
     * @staticvar Criteria实例池
     */
    public static $instances = array();
    /**
     * 获取一个Criteria实例
     * @param string|integer $name
     */
    public static function newInstance($name = '') {
        if(is_string($name) && $name) {
            if(!array_key_exists($name, self::$instances)) {
                self::$instances[$name] = new Criteria();
            }
            return self::$instances[$name];
        } else if(is_numeric($name)) {
            $name = intval($name);
            if(!array_key_exists($name, self::$instances)) {
                self::$instances[$name] = new Criteria();
            }
            return self::$instances[$name];
        } else {
            $instance = new Criteria();
            self::$instances[] = $instance;
            return $instance;
        }
    }
    
    /**
     * @var Criterion实例池
     */
    private $criteria = array();
    /**
     * 构造方法
     */
    public function __construct() {
        parent::__construct(
            array(
                //在多个实例连接时做为连接关键词
                'combine' => self::COMBINE_AND
            ),
            array(
                'combine' => array(1, 2, 3, 4, 5, 6, 7, 8)
            )
        );
    }
    /**
     * 在Criterion实例池尾插入一个Criterion实例或Criterion实例数组
     * @param Criterion|array<Criterion> $criterion
     * @param array $options see Criterion::toCriterion()
     */
    public function push($criterion, $options = array()) {
        if($criterion instanceof Criterion) {
            return array_push($this->criteria, array('criterion' => $criterion, 'options' => $options));
        } else if(is_array($criterion)) {
            foreach($criterion as $item) {
                $in = $this->push($item, $options);
            }
            return $in;
        } else {
            //TODO warning isnot an instance of Criterion
        }
    }
    /**
     * 将Criterion实例池最后一个实例弹出
     * @return Criterion
     */
    public function pop() {
        $item = array_pop($this->criteria);
        return ($item)?$item['criterion']:null;
    }
    /**
     * 在Criterion实例池开头插入一个Criterion实例或Criterion实例数组
     * @param Criterion|array<Criterion> $criterion
     * @param array $options see Criterion::toCriterion()
     */
    public function unshift($criterion, $options = array()) {
        if($criterion instanceof Criterion) {
            return array_pop($this->criteria, array('criterion' => $criterion, 'options' => $options));
        } else if(is_array($criterion)) {
            foreach($criterion as $item) {
                $in = $this->unshift($item, $options);
            }
            return $in;
        } else {
            //TODO warning isnot an instance of Criterion
        }
    }
    /**
     * 将Criterion实例池开头的实例弹出
     * @return Criterion
     */
    public function shift() {
        $item = array_shift($this->criteria);
        return ($item)?$item['criterion']:null;
    }
    
    /**
     * 将Criterial转换成子句
     * @return string
     */
    public function toCriteria() {
        $previous = '';
        foreach($this->criteria as $item) {
            $criterion = $item['criterion'];
            $options = $item['options'];
            $previous = Criterion::combine($criterion, $previous, $options);
        }
        return $previous;
    }
    /**
     * 将一个或多个Criteria实例与前子句组合成新的子句
     * @param Criteria|array<Criteria> $criteria
     * @param string $previous
     * @return string
     */
    public static function combine($criteria, $previous = null) {
        if(is_array($criteria)) {
            foreach($criteria as $item) {
                if($item instanceof Criteria) {
                    $previous = self::combine($item, $previous);
                }
            }
            $str = $previous;
        } if(is_string($previous) && $previous && ($criteria instanceof Criteria)) {
            $str = $criteria->toCriteria();
            $combine = $criteria->getCombine();
            switch($combine) {
                case self::COMBINE_AND:
                    $str = "{$previous} AND {$str}";
                    break;
                case self::COMBINE_OR:
                    $str = "{$previous} OR {$str}";
                    break;
                case self::COMBINE_BRACKET_AND:
                    $str = "({$previous} AND {$str})";
                    break;
                case self::COMBINE_BRACKET_OR:
                    $str = "({$previous} OR {$str})";
                    break;
                case self::COMBINE_NOT_AND:
                    $str = "{$previous} AND NOT {$str}";
                    break;
                case self::COMBINE_NOT_OR:
                    $str = "{$previous} OR NOT {$str}";
                    break;
                case self::COMBINE_BRACKET_NOT_AND:
                    $str = "({$previous} AND NOT {$str})";
                    break;
                case self::COMBINE_BRACKET_NOT_OR:
                    $str = "({$previous} OR NOT {$str})";
                    break;
            }
        } else if($criteria instanceof Criteria) {
            $str = $criteria->toCriteria();
        } else if(is_string($previous)) {
            $str = $previous;
        } else {
            //TODO warning
            $str = '';
        }
        return $str;
    }
}
?>