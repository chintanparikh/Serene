<?php 
/**
* 
*/

namespace Serene\Core;

class Dispatcher
{
	public $load;
	public $controllerInstance;
	
	/**
	 * Constructor function
	 * @param Load $load A Load instance 
	 * @param array $dispatchCall The dispatch call created by the Router
	 */
	public function __construct(Load $load, array $dispatchCall)
	{
		$this->load = $load;
		$this->loadController($dispatchCall['controller']);
		$this->runMethod($dispatchCall['method'], $dispatchCall['args']);				
	}

	/**
	 * Loads a controller, controller exceptions are dealt with here
	 * @param string $controller The controller name 
	 * @return void|false
	 */
	protected function loadController($controller)
	{
		$this->controllerInstance = $this->load->controller($controller);
		if ($this->controllerInstance === false)
		{
			throw new \Exception("The controller '{$controller}' could not be loaded");
			return false;
		}
	}

	/**
	 * Runs a method of the controller with the arguments supplied
	 * @param string $method 
	 * @param array $args An array of the arguments
	 * @return void
	 */
	protected function runMethod($method, array $args)
	{
		if (method_exists($this->controllerInstance, $method))
		{
			call_user_func_array(array($this->controllerInstance, $method), $args);
		}
		else
		{
			throw new \Exception("The method '{$method}' does not exist");
		}
	}



}
