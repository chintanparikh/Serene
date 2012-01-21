<?php
/**
 * This class is used for Autoloading all classes
 * For this to work, the namespacing in each class MUST follow the directory structure. E.g. anything in Serene/Core has the namespace Serene/Core.
 * 
 * @version 1.0
 * @author timtamboy63 (timtamboy63@gmail.com)
 * @license Creative Commons
 */
namespace Serene\Core;

class Autoload
{
	public static function autoload($class)
	{
		include($class .'.php');
	}
}