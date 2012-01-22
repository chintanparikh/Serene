<?php 

namespace Serene\Core\Exception;

class FileNotFound extends \Exception
{
	/**
	 * @var string file path that was not found
	 */
	private $path;

	public function __construct($path)
	{
		parent::__construct("Config file {$path} does not exist");
	}

	public function getPath()
	{
		return $this->path;
	}
}