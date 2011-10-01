<?php
/**
* 
*/
abstract class baseController
{
	protected $load, $config;

	function __construct(Load $load, Config $config)
	{
		$this->load = $load;
		$this->config = $config;
	}

	abstract function index();
}

?>