<?php 
/**
* 
*/
namespace Serene;
use Serene\Core as Core;
use Serene\Core\Exception as SereneException;

class Application
{
	public $config;
	public $load;
	public $router;

	public function __construct()
	{
		require_once('Core/Autoload.php');
	}

	public function start()
	{
		try
		{
			spl_autoload_register('Serene\Core\Autoload::autoload');
			$this->config = Core\Config::getInstance();
			$this->load = new Core\Load($this->config);
			$this->router = new Core\Router($this->config);
			$this->dispatcher = new Core\Dispatcher($this->load, $this->router->getRoute());
			$this->input = new Core\Input($this->config);
		}
		catch (SereneException\FileNotFound $e)
		{
			print "Error: {$e->getPath()} does not exist!";
		}
	}


}


