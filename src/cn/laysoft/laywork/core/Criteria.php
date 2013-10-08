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
    const COMBINE_REG_FORMAT_AND = '&';
    const COMBINE_REG_FORMAT_OR = '|';
    const COMBINE_REG_FORMAT_BRACKET_AND = '(&';
    const COMBINE_REG_FORMAT_BRACKET_OR = '(|';
    const COMBINE_REG_FORMAT_NOT_AND = '!&';
    const COMBINE_REG_FORMAT_NOT_OR = '!|';
    const COMBINE_REG_FORMAT_BRACKET_NOT_AND = '(!&';
    const COMBINE_REG_FORMAT_BRACKET_NOT_OR = '(!|';
    const EXPLODE_STRING = '&&';
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
            if(!empty($criterion)) {
                foreach($criterion as $item) {
                    $in = $this->push($item, $options);
                }
            } else {
                $in = array();
            }
            return $in;
        } else {
            //TODO warning isnot an instance of Criterion
            return array();
        }
    }
    /**
     * 将Criterion实例池最后一个实例弹出
     * @return Criterion
     */
    public function pop() {
        $item = array_pop($this->criteria);
        return ($item)?$item['criterion']:array();
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
            if(!empty($criterion)) {
                foreach($criterion as $item) {
                    $in = $this->unshift($item, $options);
                }
            } else {
                $in = array();
            }
            return $in;
        } else {
            //TODO warning isnot an instance of Criterion
            return array();
        }
    }
    /**
     * 将Criterion实例池开头的实例弹出
     * @return Criterion
     */
    public function shift() {
        $item = array_shift($this->criteria);
        return ($item)?$item['criterion']:array();
    }
    /**
     * 转换规则：&&为多个Criteria连接，&为多个Criterion连接；
     * 每个Criteria字符串首字母为“&”或空时会与前一个Criteria进行AND连接
     * 每个Criteria字符串首字母为“|”时会与前一个Criteria进行OR连接
     * 每个Criterion字符串首字母为空时会与前一个Criterion进行AND连接
     * 每个Criterion字符串首字母为“|”时会与前一个Criterion进行OR连接
     * @param string $filter
     * @return array<Criteria>
     */
    public static function parse($filter) {
        $arrCriteria = array();
        $combinePattern = '/^('.
            preg_quote(self::COMBINE_REG_FORMAT_AND).'|'.
            preg_quote(self::COMBINE_REG_FORMAT_OR).'|'.
            preg_quote(self::COMBINE_REG_FORMAT_BRACKET_AND).'|'.
            preg_quote(self::COMBINE_REG_FORMAT_BRACKET_OR).'|'.
            preg_quote(self::COMBINE_REG_FORMAT_NOT_AND).'|'.
            preg_quote(self::COMBINE_REG_FORMAT_NOT_OR).'|'.
            preg_quote(self::COMBINE_REG_FORMAT_BRACKET_NOT_AND).'|'.
            preg_quote(self::COMBINE_REG_FORMAT_BRACKET_NOT_OR).
            ')(.*)$/';
        if($filter && is_string($filter)) {
            $arrCriteriaString = explode(self::EXPLODE_STRING, $filter);
            foreach($arrCriteriaString as $criteriaString) {
                $criteria = new Criteria();
                $tmp = preg_match($combinePattern, $criteriaString, $matches);
                if($tmp) {
                    switch($matches[1]) {
                        case self::COMBINE_REG_FORMAT_AND:
                            $criteria->combine = self::COMBINE_AND;
                            break;
                        case self::COMBINE_REG_FORMAT_OR:
                            $criteria->combine = self::COMBINE_OR;
                            break;
                        case self::COMBINE_REG_FORMAT_BRACKET_AND:
                            $criteria->combine = self::COMBINE_BRACKET_AND;
                            break;
                        case self::COMBINE_REG_FORMAT_BRACKET_OR:
                            $criteria->combine = self::COMBINE_BRACKET_OR;
                            break;
                        case self::COMBINE_REG_FORMAT_NOT_AND:
                            $criteria->combine = self::COMBINE_NOT_AND;
                            break;
                        case self::COMBINE_REG_FORMAT_NOT_OR:
                            $criteria->combine = self::COMBINE_NOT_OR;
                            break;
                        case self::COMBINE_REG_FORMAT_BRACKET_NOT_AND:
                            $criteria->combine = self::COMBINE_BRACKET_NOT_AND;
                            break;
                        case self::COMBINE_REG_FORMAT_BRACKET_NOT_OR:
                            $criteria->combine = self::COMBINE_BRACKET_NOT_OR;
                            break;
                    }
                    $criteriaString = $matches[2];
                }
                
                $criteria->push(Criterion::parse($criteriaString));
                $arrCriteria[] = $criteria;
            }
            //$filters = explode(Cell::FLAG_CONNECT, $filter);
            /*$pattern = '/([%|\|]{0,1})(.*)/';
            preg_match($pattern, $filter, $matches);
            
            $pattern = '/^(.*)(['.
                preg_quote(self::REG_FORMAT_EQUAL).'|'.
                preg_quote(self::REG_FORMAT_UNEQUAL).'|'.
                preg_quote(self::REG_FORMAT_GREATER).'|'.
                preg_quote(self::REG_FORMAT_LESS).'|'.
                preg_quote(self::REG_FORMAT_GREATER_EQUAL).'|'.
                preg_quote(self::REG_FORMAT_LESS_EQUAL).'|'.
                preg_quote(self::REG_FORMAT_IN).'|'.
                preg_quote(self::REG_FORMAT_NOT_IN).'|'.
                preg_quote(self::REG_FORMAT_EXISTS).'|'.
                preg_quote(self::REG_FORMAT_NOT_EXISTS).'|'.
                preg_quote(self::REG_FORMAT_LIKE).'|'.
                preg_quote(self::REG_FORMAT_UNLIKE).'|'.
                preg_quote(self::REG_FORMAT_LEFT_LIKE).'|'.
                preg_quote(self::REG_FORMAT_RIGHT_LIKE).'|'.
                preg_quote(self::REG_FORMAT_LEFT_UNLIKE).'|'.
                preg_quote(self::REG_FORMAT_RIGHT_UNLIKE).'|'.
                preg_quote(self::REG_FORMAT_BETWEEN).']{1})(.*)$/';
            preg_match($pattern, $filter, $matches);
            $filters = explode(Cell::FLAG_CONNECT, $filter);*/
        } else {
            //TODO warning filter isnot string
        }
        return $arrCriteria;
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