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
			$xml = new SimpleXMLElement($raw);

			$vars = get_object_vars($xml);
			foreach ($vars as $key=>$value)
			{
				$name = $key;
			}
			
			// Remove the comments field (uncessary, and we don't need it))
			unset($xml->$name->comment);
			$config[$name] = get_object_vars($xml->$name);
		}
		else
		{
			throw new \Exception("Config file {$path} does not exist!"); 
		}

		return $config;
		
	}
}