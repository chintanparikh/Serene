<?php 
/**
* 
*/
namespace Serene\Controllers;

use Serene\Core\Base as Base;

class Test extends Base\Controller
{
	public function __construct()
	{		
	}
	public function index()
	{
	}

	public function test()
	{
		$args = func_get_args();
		foreach ($args as $argument)
		{
			// Do something
		}
	}
}

