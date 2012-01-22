<?php

namespace Serene\Core\ConfigParsers;
use Serene\Core\Base as Base;

class XML extends Base\ConfigParser
{
	public function parse($path)
	{
		$this->validatePath($path);

		$config = array();

		$xml = simplexml_load_file($path);

		$vars = get_object_vars($xml);
		foreach ($vars as $key=>$value)
		{
			$name = $key;
		}
		
		if (!empty($name)) {
			// Remove the comments field (uncessary, and we don't need it))
			unset($xml->$name->comment);
			$config[$name] = get_object_vars($xml->$name);
		}

		return $config;
	}
}