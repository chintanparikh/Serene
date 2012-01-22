<?php

namespace Serene\Core\ConfigParsers;
use Serene\Core\Base as Base;

class XML extends Base\ConfigParser
{
	public function parse($path)
	{
		if (file_exists($path))
		{
			$raw = file_get_contents($path);
			$config = json_decode($raw, true);
		}
		else
		{
			throw new \Exception("Config file {$path} does not exist!"); 
		}

		return $config;
		
	}
}