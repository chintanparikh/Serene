<?php 
/**
 * 
 */

namespace Serene\Core\Base;
use Serene\Core\Exception as SereneException;

abstract class ConfigParser
{
	/**
	 * All parser classes must have a parse method
	 * @param string $path The config File
	 * @return array An associative array containing the $config
	 */
	abstract public function parse($path);

	/**
	 * Helper function that validates if the path exists
	 * @param string $path The config File
	 * @throws Exception
	 */
	public function validatePath($path)
	{
		if (!file_exists($path))
		{
			throw new SereneException\FileNotFound($path);
		}
		else
		{
			return true;
		}
	}
}