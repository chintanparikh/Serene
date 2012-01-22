<?php

namespace Serene\Core\ConfigParsers;
use Serene\Core\Base as Base;

class INI extends Base\ConfigParser
{
	public function parse($path)
	{
		$this->validatePath($path);

		$config = parse_ini_file($path, true);

		return $config;
	}
}