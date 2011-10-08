<?php 
/**
* 
*/
namespace Serene\Model;
use Serene\Core\Base as Base;

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

?>