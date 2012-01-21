<?php 
/**
* 
*/
namespace Serene\Models;
use Serene\Core\Base as Base;
use Serene\Core\Libraries as Library;

class Model extends Base\Model
{
	
	function __construct()
	{
		# code...			
	}

	function dummyFunction()
	{
		$dummyArray = array(
							'first' => 'First',
							'last' => 'Last'
							);
		return $dummyArray;
	}
}
