<?php

namespace Serene\Core\ConfigParsers;
use Serene\Core\Base as Base;

class PHP extends Base\ConfigParser
{
	public function parse($path)
	{
		$this->validatePath($path);

		$config = array();

		/*
		 * $config is the array found in ALL PHP  config files stored in $this->pathToConfig/
		 */
		return $config;
		
	}
}