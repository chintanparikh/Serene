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
		$this->path = $path;
		parent::__construct("{$path} does not exist");
	}

	public function getPath()
	{
		return $this->path;
	}
}