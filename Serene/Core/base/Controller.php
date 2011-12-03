<?php
/**
* 
*/

namespace Serene\Core\Base;
use Serene\Core as Core;

abstract class Controller
{
	protected $load, $config;

	function __construct(Core\Load $load, Core\Config $config)
	{
		$this->load = $load;
		$this->config = $config;
	}

	abstract function index();
}
