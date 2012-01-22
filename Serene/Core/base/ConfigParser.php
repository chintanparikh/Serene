<?php 
/**
 * 
 */

namespace Serene\Core\Base;

abstract class ConfigParser
{
	/**
	 * All parser classes must have a parse method
	 * @param type $file The config File
	 * @return array An associative array containing the $config
	 */
	abstract public function parse($path);
}