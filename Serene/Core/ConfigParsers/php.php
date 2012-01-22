<?php

namespace Serene\Core\ConfigParsers;
use Serene\Core\Base as Base;

class PHP extends Base\ConfigParser
{
	public function parse($path)
	{
		$config = false;
		if (file_exists($path))
		{
			require($path);
		}
		else
		{
			throw new \Exception("Config file {$path} does not exist!");
		}
		/*
		 * $config is the array found in ALL PHP  config files stored in $this->pathToConfig/
		 */
		return $config;
		
	}
}