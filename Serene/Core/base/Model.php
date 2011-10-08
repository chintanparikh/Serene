<?php

/**
* 
*/

namespace Serene\Core\Base;

abstract class Model
{
	protected $load, $config;

	function __construct(Load $load, Config $config)
	{
		$this->load = $load;
		$this->config = $config;
	}
}

?>