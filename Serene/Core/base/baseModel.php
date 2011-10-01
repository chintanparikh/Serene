<?php

/**
* 
*/
abstract class baseModel
{
	protected $load, $config;

	function __construct(Load $load, Config $config)
	{
		$this->load = $load;
		$this->config = $config;
	}
}

?>