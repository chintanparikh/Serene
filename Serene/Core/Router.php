<?php 
/**
* 
*/
class Router
{
	/**
	 * Holds a Config Instance
	 *
	 * @var Config 
	 */
	protected $config;

	/**
	 * Holds the URI after the path has been removed from it
	 *
	 * @var string
	 */
	protected $URI;

	/**
	 * Holds an Array of Route Instances
	 *
	 * @var array
	 */
	protected $routes;

	
	/**
	 * Constructor function
	 * Ensures there is a $config instance,
	 * Sets up the $this->routes array
	 * Gets the URI
	 *
	 * @access public
	 * @param Config $config An instance of the Config class
	 * @param string $requestURI The URI to use (If none provided, it is automatically detected)
	 */
	public function __construct(Config $config, $requestURI = '')
	{
		$this->config = $config;
		$this->routes = array();

		/*
		 * Get the URI
		 */
		$this->URI = $this->getURI($requestURI);
	}

	/**
	 * Gets and formats the URI if '' provided, otherwise formats and returns provided URI
	 *
	 * @access protected
	 * @param string $requestURI Provided URI
	 * @return string The URI to use for this instance 
	 */
	public function getURI($requestURI)
	{
		if ($requestURI == '')
		{
			$URI = filter_input(INPUT_SERVER, "REQUEST_URI", FILTER_SANITIZE_URL);
		}
		else
		{
			$URI = $requestURI;
		}

		/*
		 * Trim the '/' from the URI, then remove the path it is stored under (by default 'Portfolio/')
		 */
		$URI = trim($URI, '/');
		$URI = strtolower($URI);
		$URI = preg_replace('~' . strtolower($this->config->router('path')) . '~', '', $URI, 1);
		$URI = ltrim($URI, '/');
		/*
		 * If the path is localhost/Portfolio/A/B/C/,
		 * $URI = A/B/C
		 */
		return $URI;
	}

	/**
	 * Adds a Route the $this->routes
	 *
	 * @access public
	 * @param Route $route 
	 * @return null
	 */
	public function add(BaseRoute $route)
	{
		array_push($this->routes, $route);		
	}

	/**
	 * Does the Routing based on the $this->routes array. Each Route instance should specify how to get the controller, method and args
	 * 
	 * @access public
	 * @return array The array that the Dispatcher will understand
	 */
	public function getRoute()
	{
		// Add the default Routing pattern, in case no routes are defined
		$this->add(new Route('{*}', $this->config->router('defaultRoutingPattern')));
		foreach ($this->routes as $route)
		{
			// If our route's path matches our URI
			if ($route->matches($this->URI))
			{
				return array('controller' => $route->getController($this->URI),
							 'method' => $route->getMethod($this->URI),
							 'args' => $route->getArgs($this->URI)
							 );
			}
		}
	}
}

?>