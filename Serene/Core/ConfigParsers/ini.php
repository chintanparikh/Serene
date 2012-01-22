<?php

namespace Serene\Core\ConfigParsers;
use Serene\Core\Base as Base;

class INI extends Base\ConfigParser
{
	public function parse($path)
	{
		if (file_exists($path))
		{
			$config = parse_ini_file($path, true);
		}
		else
		{
			throw new \Exception("Config file {$path} does not exist!"); 
		}
		return $config;
		
	}
}