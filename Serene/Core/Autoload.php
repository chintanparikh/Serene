<?php

namespace Serene\Core;

class Autoload
{
	public static function autoload($class)
	{
		$classParts = explode('\\', $class);

	
		array_shift($classParts);
		$filename = BASE_PATH;
		foreach ($classParts as $classPart)
		{
			$filename .= '/' . $classPart;
		}
		$filename .= '.php';
		include($class .'.php');

	}
}