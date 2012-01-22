<?php

namespace Serene\Core\ConfigParsers;
use Serene\Core\Base as Base;

class XML extends Base\ConfigParser
{
	public function parse($path)
	{
		$this->validatePath($path);

		$raw = file_get_contents($path);
		$config = json_decode($raw, true);

		return $config;
		
	}
}