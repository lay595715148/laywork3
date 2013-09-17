<?php
/**
 * 转换工具类,用于模板引擎输出内容
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
use Laywork;
if(!defined('INIT_LAYWORK')) { exit; }

/**
 * 转换工具类,用于模板引擎输出内容
 * @author Lay Li
 */
class Parser extends Base {
    /**
     * xml format string to php array
     * 
     * @param string $xml xml format string
     * @param bool $simple if use simplexml,default false
     * @return array|bool
     */
    public static function xml2Array($xml,$simple = false) {
        if(!is_string($xml)) {
            return false;
        }
        if($simple) {
            $xml = @simplexml_load_string($xml);
        } else {
            $xml = @json_decode(json_encode((array) simplexml_load_string($xml)),1);
        }
        return $xml;
    }
    /**
     * php array to xml format string
     * 
     * @param array $value convert array
     * @param string $root xml root tag
     * @param string $encoding xml encoding
     * @return string 
     */
    public static function array2XML($value, $root='root', $encoding='utf-8') {
        if( !is_array($value) && !is_string($value) && !is_bool($value) && !is_numeric($value) && !is_object($value) ) {
            return false;
        }
        return simplexml_load_string('<?xml version="1.0" encoding="'.$encoding.'"?>'.self::x2str($value,$root))->asXml();
    }
    /**
     * object or array to xml format string
     * 
     * @param object $xml array or object
     * @param string $key tag name
     * @return string 
     */
    private static function x2str($xml,$key) {
        if (!is_array($xml) && !is_object($xml)) {
            return "<$key>".htmlspecialchars($xml)."</$key>";      
        }
        $xml_str='';
        foreach ($xml as $k=>$v) {   
            if(is_numeric($k)) {
                $k = '_'.$k;
            }
            $xml_str.=self::x2str($v,$k);       
        }    
        return "<$key>$xml_str</$key>"; 
    }
}
?>
