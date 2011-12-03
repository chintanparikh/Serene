<?php 
/**
* 
*/

namespace Serene\Core;

class Dispatcher
{
	public $load;
	public $controllerInstance;
	
	public function __construct(Load $load, array $dispatchCall)
	{
		$this->load = $load;
		$this->loadController($dispatchCall['controller']);
		$this->runMethod($dispatchCall['method'], $dispatchCall['args']);				
	}

	protected function loadController($controller)
	{
		$this->controllerInstance = $this->load->controller($controller);
		if ($this->controllerInstance === false)
		{
			throw new \Exception("The controller '{$controller}' could not be loaded");
			return false;
		}
	}

	protected function runMethod($method, array $args)
	{
		if (method_exists($this->controllerInstance, $method))
		{
			call_user_func_array( array($this->controllerInstance, $method), $args );
		}
		else
		{
			throw new \Exception("The method '{$method}' does not exist");
		}
	}



}
