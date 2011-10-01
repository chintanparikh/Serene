<?php 
/**
* 
*/
class Controller extends baseController
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