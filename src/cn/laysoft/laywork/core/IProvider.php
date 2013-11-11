<?php
/**
 * 生成器接口
 * @see https://github.com/lay595715148/laywork3
 * 
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
if(!defined('INIT_LAYWORK')) { exit; }

interface IProvider {
	/**
	 * provide object instance
	 * @param string|array $name name string or config array
	 */
	public function provide($name = '');
}
?>