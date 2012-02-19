<?php 
/**
* 
*/
namespace Serene\Core;

class Input
{
	protected $config;
	protected $autoSanitize;
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->autoSanitize = $this->config->input('autoXssSantize');
	}

	protected function xssSanitize(&$data)
	{
		$data = htmlentities($data);
	}

	public function post($name)
	{
		if ($this->autoSanitize)
		{
			$this->xssSanitize($_POST[$name]);
		}
		return $_POST[$name];
	}

	public function get($name)
	{
		if ($this->autoSanitize)
		{
			$this->xssSanitize($_GET[$name]);
		}
		return $_GET[$name];
	}
}