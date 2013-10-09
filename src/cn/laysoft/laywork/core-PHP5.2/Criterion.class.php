<?php
/**
 * SQL条件部分子句元素类
 * @see https://github.com/lay595715148/laywork3
 * 
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
if(!defined('INIT_LAYWORK')) { exit; }

class Criterion extends Bean {
    const OPERATOR_EQUAL = 1;
    const OPERATOR_UNEQUAL = 2;
    const OPERATOR_GREATER = 3;
    const OPERATOR_LESS = 4;
    const OPERATOR_GREATER_EQUAL = 5;
    const OPERATOR_LESS_EQUAL = 6;
    const OPERATOR_IN = 7;
    const OPERATOR_NOT_IN = 8;
    const OPERATOR_EXISTS = 9;
    const OPERATOR_NOT_EXISTS = 10;
    const OPERATOR_LIKE = 11;
    const OPERATOR_UNLIKE = 12;
    const OPERATOR_LEFT_LIKE = 13;
    const OPERATOR_RIGHT_LIKE = 14;
    const OPERATOR_LEFT_UNLIKE = 15;
    const OPERATOR_RIGHT_UNLIKE = 16;
    const OPERATOR_BETWEEN = 17;
    const REG_FORMAT_EQUAL = ':=';
    const REG_FORMAT_UNEQUAL = ':~';
    const REG_FORMAT_GREATER = ':>';
    const REG_FORMAT_LESS = ':<';
    const REG_FORMAT_GREATER_EQUAL = ':>=';
    const REG_FORMAT_LESS_EQUAL = ':<=';
    const REG_FORMAT_IN = ':+';
    const REG_FORMAT_NOT_IN = ':-';
    const REG_FORMAT_EXISTS = ':)';
    const REG_FORMAT_NOT_EXISTS = ':(';
    const REG_FORMAT_LIKE = ':%';
    const REG_FORMAT_UNLIKE = ':^';
    const REG_FORMAT_LEFT_LIKE = ':[%';
    const REG_FORMAT_RIGHT_LIKE = ':]%';
    const REG_FORMAT_LEFT_UNLIKE = ':[^';
    const REG_FORMAT_RIGHT_UNLIKE = ':]^';
    const REG_FORMAT_BETWEEN = ':{}';
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
    const EXPLODE_STRING = '&';
    const TYPE_STRING = 1;
    const TYPE_NUMBER = 2;
    
    /**
     * 构造方法
     */
    public function __construct() {
        parent::__construct(
            array(
                'name' => '',
                'type' => self::TYPE_STRING,
                'value' => '',
                'operator' => self::OPERATOR_EQUAL,
                //在多个实例连接时做为连接关键词
                'combine' => self::COMBINE_AND
            ),
            array(
                'name' => 'string',
                'type' => array(1, 2),
                'value' => 'string',
                'operator' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17),
                'combine' => array(1, 2, 3, 4, 5, 6, 7, 8)
            )
        );
    }
    /**
     * 转换规则：&&为多个Criteria连接，&为多个Criterion连接；
     * 每个Criteria字符串首字母为“&”或空时会与前一个Criteria进行AND连接
     * 每个Criteria字符串首字母为“|”时会与前一个Criteria进行OR连接
     * 每个Criterion字符串首字母为空时会与前一个Criterion进行AND连接
     * 每个Criterion字符串首字母为“|”时会与前一个Criterion进行OR连接
     */
    public static function parse($filter) {
        $arrCriterion = array();
        $pattern = '/^(.*)('.
            preg_quote(self::REG_FORMAT_EQUAL).'|'.
            preg_quote(self::REG_FORMAT_UNEQUAL).'|'.
            preg_quote(self::REG_FORMAT_GREATER_EQUAL).'|'.
            preg_quote(self::REG_FORMAT_LESS_EQUAL).'|'.
            preg_quote(self::REG_FORMAT_GREATER).'|'.
            preg_quote(self::REG_FORMAT_LESS).'|'.
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
            preg_quote(self::REG_FORMAT_BETWEEN).
            ')(.*)$/';
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
            $arrCriterionString = explode(self::EXPLODE_STRING, $filter);
            foreach($arrCriterionString as $criterionString) {
                $criterion = new Criterion();
                $tmp = preg_match($combinePattern, $criterionString, $matches);
                if($tmp) {//检测combine连接符
                    switch($matches[1]) {
                        case self::COMBINE_REG_FORMAT_AND:
                            $criterion->combine = self::COMBINE_AND;
                            break;
                        case self::COMBINE_REG_FORMAT_OR:
                            $criterion->combine = self::COMBINE_OR;
                            break;
                        case self::COMBINE_REG_FORMAT_BRACKET_AND:
                            $criterion->combine = self::COMBINE_BRACKET_AND;
                            break;
                        case self::COMBINE_REG_FORMAT_BRACKET_OR:
                            $criterion->combine = self::COMBINE_BRACKET_OR;
                            break;
                        case self::COMBINE_REG_FORMAT_NOT_AND:
                            $criterion->combine = self::COMBINE_NOT_AND;
                            break;
                        case self::COMBINE_REG_FORMAT_NOT_OR:
                            $criterion->combine = self::COMBINE_NOT_OR;
                            break;
                        case self::COMBINE_REG_FORMAT_BRACKET_NOT_AND:
                            $criterion->combine = self::COMBINE_BRACKET_NOT_AND;
                            break;
                        case self::COMBINE_REG_FORMAT_BRACKET_NOT_OR:
                            $criterion->combine = self::COMBINE_BRACKET_NOT_OR;
                            break;
                    }
                    $criterionString = $matches[2];
                }
                
                $tmp = preg_match($pattern, $criterionString, $matches);
                if($tmp) {
                    $criterion->name = $matches[1];
                    $criterion->value = $matches[3];
                    switch($matches[2]) {
                        case self::REG_FORMAT_EQUAL:
                            $criterion->operator = self::OPERATOR_EQUAL;
                            break;
                        case self::REG_FORMAT_UNEQUAL:
                            $criterion->operator = self::OPERATOR_UNEQUAL;
                            break;
                        case self::REG_FORMAT_GREATER:
                            $criterion->operator = self::OPERATOR_GREATER;
                            break;
                        case self::REG_FORMAT_LESS:
                            $criterion->operator = self::OPERATOR_LESS;
                            break;
                        case self::REG_FORMAT_GREATER_EQUAL:
                            $criterion->operator = self::OPERATOR_GREATER_EQUAL;
                            break;
                        case self::REG_FORMAT_LESS_EQUAL:
                            $criterion->operator = self::OPERATOR_LESS_EQUAL;
                            break;
                        case self::REG_FORMAT_IN:
                            $criterion->operator = self::OPERATOR_IN;
                            break;
                        case self::REG_FORMAT_NOT_IN:
                            $criterion->operator = self::OPERATOR_NOT_IN;
                            break;
                        case self::REG_FORMAT_EXISTS:
                            $criterion->operator = self::OPERATOR_EXISTS;
                            break;
                        case self::REG_FORMAT_NOT_EXISTS:
                            $criterion->operator = self::OPERATOR_NOT_EXISTS;
                            break;
                        case self::REG_FORMAT_LIKE:
                            $criterion->operator = self::OPERATOR_LIKE;
                            break;
                        case self::REG_FORMAT_UNLIKE:
                            $criterion->operator = self::OPERATOR_UNLIKE;
                            break;
                        case self::REG_FORMAT_LEFT_LIKE:
                            $criterion->operator = self::OPERATOR_LEFT_LIKE;
                            break;
                        case self::REG_FORMAT_RIGHT_LIKE:
                            $criterion->operator = self::OPERATOR_RIGHT_LIKE;
                            break;
                        case self::REG_FORMAT_LEFT_UNLIKE:
                            $criterion->operator = self::OPERATOR_LEFT_UNLIKE;
                            break;
                        case self::REG_FORMAT_RIGHT_UNLIKE:
                            $criterion->operator = self::OPERATOR_RIGHT_UNLIKE;
                            break;
                        case self::REG_FORMAT_BETWEEN:
                            $criterion->operator = self::OPERATOR_BETWEEN;
                            break;
                    }
                    $arrCriterion[] = $criterion;
                } else {
                    //TODO warning not regular
                }
            }
        } else {
            //TODO warning filter isnot string
        }
        return $arrCriterion;
    }
    /**
     * 将Criterion对象转换为SQL
     * IN,NOTIN:$value will be explode by ','
     * BETWEEN:$value will be explode by ';'
     * @param array $options name default:subquery default false,forcequote default true,escape default true,backquote default true
     */
    public function toCriterion($options = array()) {
        $subquery = array_key_exists('subquery', $options)?$options['subquery']:false;
        $forcequote = array_key_exists('forcequote', $options)?$options['forcequote']:true;
        $escape = array_key_exists('escape', $options)?$options['escape']:true;
        $backquote = array_key_exists('backquote', $options)?$options['backquote']:true;
        $sql   = '';
        $name  = &$this->name;
        $type  = &$this->type;
        $value = &$this->value;
        
        $forcequote = ($subquery === true)?'':($type == self::TYPE_STRING || ($forcequote === true && $subquery !== true))?"'":'';
        $symbolleft = ($subquery === true)?'(':($type == self::TYPE_STRING || ($forcequote === true && $subquery !== true))?"'":'';
        $symbolright = ($subquery === true)?')':($type == self::TYPE_STRING || ($forcequote === true && $subquery !== true))?"'":'';
        $backquote = ($backquote === true)?'`':'';
        switch($this->operator) {
            case self::OPERATOR_EQUAL:
                if($subquery !== true && $escape === true) { $value = mysql_escape_string($value); }
                $sql = "{$backquote}{$name}{$backquote} = {$symbolleft}{$value}{$symbolright}";
                break;
            case self::OPERATOR_UNEQUAL:
                if($subquery !== true && $escape === true) { $value = mysql_escape_string($value); }
                $sql = "{$backquote}{$name}{$backquote} <> {$symbolleft}{$value}{$symbolright}";
                break;
            case self::OPERATOR_GREATER:
                if($subquery !== true && $escape === true) { $value = mysql_escape_string($value); }
                $sql = "{$backquote}{$name}{$backquote} > {$symbolleft}{$value}{$symbolright}";
                break;
            case self::OPERATOR_LESS:
                if($subquery !== true && $escape === true) { $value = mysql_escape_string($value); }
                $sql = "{$backquote}{$name}{$backquote} < {$symbolleft}{$value}{$symbolright}";
                break;
            case self::OPERATOR_GREATER_EQUAL:
                if($subquery !== true && $escape === true) { $value = mysql_escape_string($value); }
                $sql = "{$backquote}{$name}{$backquote} >= {$symbolleft}{$value}{$symbolright}";
                break;
            case self::OPERATOR_LESS_EQUAL:
                if($subquery !== true && $escape === true) { $value = mysql_escape_string($value); }
                $sql = "{$backquote}{$name}{$backquote} <= {$symbolleft}{$value}{$symbolright}";
                break;
            case self::OPERATOR_IN:
                if($subquery !== true) {
                    $value = explode(',', $value);
                    foreach($value as &$tmp) {
                        if($escape === true) $tmp = mysql_escape_string($tmp);
                        $tmp = $forcequote.$tmp.$forcequote;
                    }
                    $value = implode(', ', $value);
                }
                $sql = "{$backquote}{$name}{$backquote} IN ({$value})";
                break;
            case self::OPERATOR_NOT_IN:
                if($subquery !== true) {
                    $value = explode(',', $value);
                    foreach($value as &$tmp) {
                        if($escape === true) $tmp = mysql_escape_string($tmp);
                        $tmp = $forcequote.$tmp.$forcequote;
                    }
                    $value = implode(', ', $value);
                }
                $sql = "{$backquote}{$name}{$backquote} NOT IN ({$value})";
                break;
            case self::OPERATOR_EXISTS:
                if($subquery !== true) {
                    $value = explode(',', $value);
                    foreach($value as &$tmp) {
                        if($escape === true) $tmp = mysql_escape_string($tmp);
                        $tmp = $forcequote.$tmp.$forcequote;
                    }
                    $value = implode(', ', $value);
                }
                $sql = "{$backquote}{$name}{$backquote} EXISTS ({$value})";
                break;
            case self::OPERATOR_NOT_EXISTS:
                if($subquery !== true) {
                    $value = explode(',', $value);
                    foreach($value as &$tmp) {
                        if($escape === true) $tmp = mysql_escape_string($tmp);
                        $tmp = $forcequote.$tmp.$forcequote;
                    }
                    $value = implode(', ', $value);
                }
                $sql = "{$backquote}{$name}{$backquote} NOT EXISTS ({$value})";
                break;
            case self::OPERATOR_LIKE:
                if($subquery !== true) {
                    if($escape === true) { $value = mysql_escape_string($value); }
                    $forcequote = "'";
                    $sql = "{$backquote}{$name}{$backquote} LIKE {$forcequote}%{$value}%{$forcequote}";
                } else {
                    $sql = '0';
                }
                break;
            case self::OPERATOR_UNLIKE:
                if($subquery !== true) {
                    if($escape === true) { $value = mysql_escape_string($value); }
                    $forcequote = "'";
                    $sql = "{$backquote}{$name}{$backquote} NOT LIKE {$forcequote}%{$value}%{$forcequote}";
                } else {
                    $sql = '0';
                }
                break;
            case self::OPERATOR_LEFT_LIKE:
                if($subquery !== true) {
                    if($escape === true) { $value = mysql_escape_string($value); }
                    $forcequote = "'";
                    $sql = "{$backquote}{$name}{$backquote} LIKE {$forcequote}{$value}%{$forcequote}";
                } else {
                    $sql = '0';
                }
                break;
            case self::OPERATOR_RIGHT_LIKE:
                if($subquery !== true) {
                    if($escape === true) { $value = mysql_escape_string($value); }
                    $forcequote = "'";
                    $sql = "{$backquote}{$name}{$backquote} LIKE {$forcequote}%{$value}{$forcequote}";
                } else {
                    $sql = '0';
                }
                break;
            case self::OPERATOR_LEFT_UNLIKE:
                if($subquery !== true) {
                    if($escape === true) { $value = mysql_escape_string($value); }
                    $forcequote = "'";
                    $sql = "{$backquote}{$name}{$backquote} NOT LIKE {$forcequote}{$value}%{$forcequote}";
                } else {
                    $sql = '0';
                }
                break;
            case self::OPERATOR_RIGHT_UNLIKE:
                if($subquery !== true) {
                    if($escape === true) { $value = mysql_escape_string($value); }
                    $forcequote = "'";
                    $sql = "{$backquote}{$name}{$backquote} NOT LIKE {$forcequote}%{$value}{$forcequote}";
                } else {
                    $sql = '0';
                }
                break;
            case self::OPERATOR_BETWEEN:
                if($subquery !== true) {
                    $value = explode(',', $value);
                    foreach($value as &$tmp) {
                        if($escape === true) $tmp = mysql_escape_string($tmp);
                    }
                    if(count($value) >= 2) {
                        $sql = "{$backquote}{$name}{$backquote} BETWEEN {$forcequote}{$value[0]}{$forcequote} AND {$forcequote}{$value[1]}{$forcequote}";
                    } else {
                        $sql = '0';
                    }
                } else {
                    $sql = '0';
                }
                break;
        }
        return $sql;
    }
    
    /**
     * 将Criterion与前子句组合成新的子句
     * @param Criterion $criterion
     * @param string $previous
     * @param array $options see Criterion::toCriterion()
     */
    public static function combine($criterion, $previous = null, $options = array()) {
        if(is_array($criterion)) {
            foreach($criterion as $item) {
                if($item instanceof Criterion) {
                    $previous = self::combine($item, $previous);
                }
            }
            $str = $previous;
        } else if(is_string($previous) && $previous && ($criterion instanceof Criterion)) {
            $str = $criterion->toCriterion($options);
            $combine = $criterion->getCombine();
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
        } else if($criterion instanceof Criterion) {
            $str = $criterion->toCriterion($options);
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