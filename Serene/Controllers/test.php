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
		print 'index';
	}

	public function test()
	{
		print 'YAY';
		$args = func_get_args();
		foreach ($args as $argument)
		{
			print $argument;
		}
	}
}

