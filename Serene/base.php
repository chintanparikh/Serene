<?php 
/**
* 
*/
class Application
{
	public $config;
	public $load;
	public $router;

	public function __construct()
	{
		/**
		* Require the base templates for the controllers, etc
		**/
		require_once('Core/base/baseController.php');
		require_once('Core/base/baseModel.php');
		require_once('Core/base/baseRoute.php');
		/**
		* Require all the core files for the framework
		**/
		require_once('Core/Config.php');
		require_once('Core/Load.php');
		require_once('Core/Router.php');
		require_once('Core/Route.php');
		require_once('Core/Dispatcher.php');
	}

	public function start()
	{
		$this->config = Config::getInstance();
		$this->load = new Load($this->config);
		$this->router = new Router($this->config);
		$this->dispatcher = new Dispatcher($this->load, $this->router->getRoute());
	}


}


?>