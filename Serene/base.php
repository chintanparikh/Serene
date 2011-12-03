<?php 
/**
* 
*/
namespace Serene;
use Serene\Core as Core;

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
		require_once('Core/Base/Controller.php');
		require_once('Core/Base/Model.php');
		require_once('Core/Base/Route.php');
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
		$this->config = Core\Config::getInstance();
		$this->load = new Core\Load($this->config);
		$this->router = new Core\Router($this->config);
		$this->dispatcher = new Core\Dispatcher($this->load, $this->router->getRoute());
	}


}


