<?php
namespace cn\laysoft\laywork\core;
if(!defined('INIT_LAYWORK')) { exit; }
<?php
/**
 * 基础数据模型
 * @see https://github.com/lay595715148/sensitive
 * 
 * @author liaiyong<595715148@qq.com>
 * @Version: 0.1.48 (build 130723)
 */
if(!defined('INIT_SENSITIVE')) { exit; }

/**
 * <p>基础数据模型</p>
 * <p>核心类，继承至此类的对象将会拥有setter和getter方法和build方法</p>
 * 
 * @abstract
 */
abstract class Bean extends Base {
    /**
     * class properties and default value.
     * please don't modify in all methods except for '__construct','__set','__get' and so on.
     * example: array('id'=>0,'name'=>'')
     */
    protected $properties = array();
    /**
     * class property types.
     * string[0,'string',default],number[1,'number'],integer[2,'integer'],boolean[3,'boolean'],datetime[4,'datetime'],
     *     date[5,'date'],time[6,'time'],float[7,'float'],double[8,'double'],enum[array(1,2,3)],dateformat[array('dateformat'=>'Y-m-d')],other[array('other'=>...)]...
     * example: array('id'=>'integer','name'=>0)
     */
    protected $propertype = array();
    /**
     * 构造方法
     * @param array $properties
     */
    public function __construct($properties = array(), $propertype = array()) {
        if(is_array($properties)) {
            $this->properties = $properties;
        }
        if(is_array($propertype)) {
            $this->propertype = $propertype;
        }
    }
    /**
     * isset property
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        return isset($this->properties[$name]);
    }
    /**
     * unset property
     * @param string $name
     * @return void
     */
    public function __unset($name) {
        unset($this->properties[$name]);
    }
    /**
     * magic setter,set value to class property
     * 
     * @see AbstractBase::__set()
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value) {
        $propertype = &$this->propertype;
        $properties = &$this->properties;

        if(array_key_exists($name, $properties)) {
            if(array_key_exists($name, $propertype)) {
                switch($propertype[$name]) {
                    case 0:
                    case 'string':
                        $properties[$name] = strval($value);
                        break;
                    case 1:
                    case 'number':
                        $properties[$name] = 0 + $value;
                        break;
                    case 2:
                    case 'integer':
                        $properties[$name] = intval($value);
                        break;
                    case 3:
                    case 'boolean':
                        $properties[$name] = boolval($value);
                        break;
                    case 4:
                    case 'datetime':
                        if(is_numeric($value)) {
                            $properties[$name] = date('Y-m-d H:i:s', intval($value));
                        } else if(is_string($value)) {
                            $properties[$name] = date('Y-m-d H:i:s', strtotime($value));
                        }
                        break;
                    case 5:
                    case 'date':
                        if(is_numeric($value)) {
                            $properties[$name] = date('Y-m-d', intval($value));
                        } else if(is_string($value)) {
                            $properties[$name] = date('Y-m-d', strtotime($value));
                        }
                        break;
                    case 6:
                    case 'date':
                        if(is_numeric($value)) {
                            $properties[$name] = date('H:i:s', intval($value));
                        } else if(is_string($value)) {
                            $properties[$name] = date('H:i:s', strtotime($value));
                        }
                        break;
                    case 7:
                    case 'float':
                        $properties[$name] = floatval($value);
                        break;
                    case 8:
                    case 'double':
                        $properties[$name] = doubleval($value);
                        break;
                    default:
                        if(is_array($propertype[$name])) {
                            if(array_key_exists('dateformat', $propertype[$name])) {
                                $dateformart = $propertype[$name]['dateformat'];
                                if(is_numeric($value)) {
                                    $properties[$name] = date($dateformart, intval($value));
                                } else if(is_string($value)) {
                                    $properties[$name] = date($dateformart, strtotime($value));
                                }
                            } else if(array_key_exists('other', $propertype[$name])) {
                                $properties[$name] = $this->otherformart($value, $propertype[$name]);
                            } else {
                                $key = array_search($value, $propertype[$name]);
                                if($key !== null) {
                                    $properties[$name] = $propertype[$name][$key];
                                }
                            }
                        } else {
                            $properties[$name] = strval($value);
                        }
                        break;
                }
            } else {
                $properties[$name] = $value;
            }
        } else {
            throw new PropertyNotFoundException('There is no property:'.$name.' in class:'.get_class($this));
        }
    }
    /**
     * please implement this method in sub class
     */
    protected function otherformart($value, $propertype) {
        return 'other';
    }
    /**
     * magic setter,get value of class property
     * 
     * @see AbstractBase::__get()
     * @param string $name
     * @return mixed|void
     */
    public function __get($name) {
        $properties = &$this->properties;
        
        if(array_key_exists($name, $properties)) {
            return $properties[$name];
        } else {
            throw new PropertyNotFoundException('There is no property:'.$name.' in class:'.get_class($this));
        }
    }
    /**
     * magic call method,auto call setter or getter
     * 
     * @see AbstractBase::__call()
     * @param string $method
     * @param array $arguments
     * @return mixed|void
     */
    public function __call($method, $arguments) {
        if(method_exists($this,$method)) {
            return (call_user_func_array(array($this, $method), $arguments));
        } else {
            $properties = &$this->properties;
            $keys = array_keys($properties);
            $lower = array();
            foreach($keys as $i=>$key) {
                $lower[$i] = strtolower($key);
            }
            
            if(strtolower(substr($method, 0, 3)) === 'get') {
                $proper = strtolower(substr($method, 3));
                $index = array_search($proper,$lower);
                if($index !== null) {
                    return $this->{$properties[$keys[$index]]};
                } else {
                    return $this->{$proper};
                }
            } else if(strtolower(substr($method, 0, 3)) === 'set'){
                $proper = strtolower(substr($method, 3));
                $index = array_search($proper,$lower);
                if($index !== null) {
                    $this->{$properties[$keys[$index]]} = $arguments[0];
                } else {
                    $this->{$proper} = $arguments[0];
                }
            } else {
                throw new MethodNotFoundException('There is no method:'.$method.'( ) in class:'.get_class($this));
            }
        }
    }

    /**
     * to array value of class properties
     * 
     * @return array
     */
    public function toArray() {
        return $this->properties;
    }

    /**
     * read values from variables(super global varibles or user-defined variables) then auto inject to this.
     * default read from $_REQUEST
     * @param integer|array $scope
     * @return void|Bean
     */
    public function build($scope = 0) {
        if(is_numeric($scope) || !$scope) {
            $scope = &Scope::parseScope($scope);
        } else if(!is_array($scope)){
            throw new BeanScopeException('There is a type error in class:'.get_class($this).' method:build( ) param:$scope');
        }
        foreach($this->toArray() as $k=>$v) {
            if(array_key_exists($k, $scope)) {
                $this->$k = $scope[$k];
            }
        }
        return $this;
    }
}

/**
 * Bean Scope Exception
 * @author liaiyong
 * @abstract
 */
class BeanScopeException extends \Exception {}
?>