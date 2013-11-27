<?php
/**
 * teplate生成器接口
 * @see https://github.com/lay595715148/laywork3
 * 
 * @author Lay Li
 * @version: 0.0.1 (build 130911)
 */
namespace cn\laysoft\laywork\core;
use cn\laysoft\laywork\core\IProvider;
if(!defined('INIT_LAYWORK')) { exit; }

interface ITemplateProvider extends IProvider {
	/**
	 * @see \cn\laysoft\laywork\core\IProvider::provide()
	 * @return Template
	 */
}
?>