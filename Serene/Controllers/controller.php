<?php 
/**
* 
*/
namespace Serene\Controller;

use Serene\Core\Base as Base;

class Controller extends Base\Controller
{

	function index()
	{
		$model = $this->load->model('model');
		$data = $model->dummyFunction();

		$this->load->view('view', $data);
	}

	function test()
	{
		print 'test';
	}
}

?>