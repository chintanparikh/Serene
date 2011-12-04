<?php 
/**
 * Route Class - Creates Routes to be used by the Router
 *
 * Special values: (All these are changable via the constants defined below (WILDCARD, IGNORE, etc))
 *  {*} = Wildcard. Anything will match this. Used in Path. MUST be last in the Path
 *  {0} = Ignore. Used in Pattern. For example, new Route('Blog/A/B/C', '{0}/{controller}/{method}/{args}') would set the Controller to A, method to B and args to C. The 'Blog' would be ignored
 *  {controller} = Set the controller. Used in Pattern
 *  {method} = Set the method. Used in Pattern
 *  {args} = Anything + including {args} becomes an array of args passed to the method. Used in Pattern
 *
 * In the Pattern, the first string is presumed to be controller, the next is presumed to be the method and anything following will be used as arguments
 * Some examples:
 *  new Route('blog/{*}', 'blog_controller/{method}/{args}');
 *  new Route('someString/aController/{*}', '{0}/{controller}/{method}/{args}');
 *
 # What needs to be done:
 #  Need to delegate all valdation of the Route into validate()
 *
 */

namespace Serene\Core;

use Serene\Core\Base as Base;

class Route implements Base\Route
{
	/**
	 * Constants for the patterns, paths, defaults.
	 */
	const WILDCARD = '{*}';
	const IGNORE = '{0}';
	const CONTROLLER = '{controller}';
	const METHOD = '{method}';
	const ARGS = '{args}';
	const PATTERN_REGEX = '~\{[a-x0-9]+?\}~';
	const DEFAULT_CONTROLLER = 'defaultController';
	const DEFAULT_METHOD = 'defaultMethod';

	/**
	 * The path that the Route will apply for
	 * Shouldn't start or end with a '/'
	 *
	 * @var string
	 */
	public $path;
	protected $pathParts;

	/**
	 * The routing pattern that determines the route
	 * Shouldn't start or end with a '/'
	 *
	 * @var string
	 */
	public $pattern;
	protected $patternParts;

	/**
	 * Holds the Config instance
	 *
	 * @var Config
	 */
	protected $config;

	/**
	 * Stores the position of the controller
	 *
	 * @var int
	 */
	protected $controllerPosition;

	/**
	 * Stores the position of the method
	 *
	 * @var int
	 */	
	protected $methodPosition;

	public function __construct($path, $pattern, $config = NULL)
	{
		$this->path = $this->renderPath($path);
		$this->pattern = $this->renderPath($pattern);
		$this->config = $this->getConfig($config);
		$this->patternParts = explode('/', $this->pattern);
		$this->pathParts = explode('/', $this->path);
		$this->validate();
	}

	/**
	 * Ensures we have a Config instance, and supplies it to the class
	 *
	 * @access protected
	 * @param null|Config $config
	 * @return Config
	 */
	protected function getConfig($config)
	{
		if ($config == NULL)
		{
			return Config::getInstance();
		}
		elseif ($config instanceof Config)
		{
			return $config;
		}
		else
		{
			throw new \Exception("Config not supplied correct in Route.php");
		}
	}
	
	/**
	 * Renders the path so it can be properly searched.
	 # Need to change it so that it doesn't modify the original paths, and instead compares case-insensitively.
	 # Current forces the URI to be lower case in output
	 *
	 * @access protected
	 * @param string $path
	 * @return string
	 */
	protected function renderPath($path)
	{
		return strtolower($path);
	}

	/**
	 * Validates the route
	 # Need to delegate all validation into here, instead of being distributed through get*
	 */
	protected function validate()
	{	}

	/**
	 * If the path is more specific than the URI, it can't match the URI.
	 *
	 * @access protected
	 * @param array $pathParts
	 * @param array $uriParts
	 * @return bool
	 */
	protected function isPathTooLong($pathParts, $uriParts)
	{
		return count($pathParts) - 1 > count($uriParts);
	}

	/**
	 * Determines whether the path stored in $this->path matches the supplied URI
	 *
	 * @access public
	 * @param string $URI
	 * @return bool
	 */
	public function matches($URI)
	{
		$URI = strtolower($URI);
		$uriParts = array();
		if (empty($URI))
		{
			$uriParts = explode('/', $URI, count($this->pathParts));
		}
		
		if ($this->isPathTooLong($this->pathParts, $uriParts))
		{
			return false;
		}

		foreach ($this->pathParts as $element=>$pathPart)
		{
			/*
			 * If the segment = {*}, then we know the rest of the string matches, because {*} matches anything, and must come last
			 */
			if ($pathPart == self::WILDCARD)
			{
				return true;
			}
			/*
			 * If we are here, $pathPart is a string. Thus, if the string matches the corresponding $uriPart, then we move on the the next segment, otherwise return false 
			 */
			elseif ($pathPart != $uriParts[$element])
			{
				return false;
			}
		}
	}

	/**
	 * Determines whether {controller}, {method}, etc exists in $this->patternParts
	 *
	 * @access protected
	 * @param string $type Either 'controller' or 'method'
	 * @return bool
	 */
	protected function segmentExistsInPath($type)
	{
		switch ($type)
		{
			case 'controller':
				$key = self::CONTROLLER;
				break;
			case 'method':
				$key = self::METHOD;
				break;
			default:
				throw new Exception('Incorrect value for $type in Route::segmentExistsInPath(). Must be either "controller" or "method"');
				break;
		}
		return in_array($key, $this->patternParts);
	}

	/**
	 * Determines (and returns) if an explicit (string) controller has been set in the Pattern
	 *
	 * @access protected
	 * @param string $type Either 'controller' or 'method'
	 * @param string $patternPart
	 * @param int $position
	 * @return bool|string
	 */
	protected function returnExplicit($type, $patternPart, $position)
	{
		if (preg_match(self::PATTERN_REGEX, $patternPart) != 1)
		{
			if ($this->segmentExistsInPath($type))
			{
				throw new \Exception("Both a string and parameter {$type} have been set in the Pattern");
				return false;
			}
			else
			{
				$typePosition = 'this->' . $type . 'Position';
				$$typePosition = $position;
				return $patternPart;
			}
		}
	}

	/**
	 * Public frontend(?) to getController. Used so there's no chance of $position being set when getController() is called
	 *
	 * @access public
	 * @param string $URI
	 * @return function
	 */
	public function controller($URI)
	{
		return $this->getController($URI);
	}

	/**
	 * Determines the controller from the pattern stored in $this->path and the supplied URI
	 *
	 * @access protected
	 * @param string $URI
	 * @param int $position Allows recursion
	 * @return string
	 */
	protected function getController($URI, $position = 0)
	{
		$uriParts = explode('/', $URI);

		/*
		 * If the URI is empty (index), set it to an empty array instead of an array with [0] = ''
		 */
		$uriParts = (empty($uriParts[0])) ? array() : $uriParts;

		if (isset($this->patternParts[$position]))
		{
			$patternPart = $this->patternParts[$position];
		}
		else
		{
			$this->controllerPosition = -1;
			return $this->config->router(self::DEFAULT_CONTROLLER);
		}

		$explicitController = $this->returnExplicit('controller', $patternPart, $position);
		if ($explicitController)
		{
			return $explicitController;
		}
		/*
		 * Here, if the controller is not explcitly set by the pattern, but rather a {controller} is used, and the controller must be extracted from the URI
		 */
		if ($patternPart === self::CONTROLLER && $position <= count($uriParts) - 1)
		{
			$this->controllerPosition = $position;
			return $uriParts[$position];
		}
		elseif ($patternPart === self::IGNORE)
		{
			return $this->getController($URI, $position + 1);
		}

		/*
		 * If nothing else matches, return the default controller
		 */
		$this->controllerPosition = -1;
		return $this->config->router(self::DEFAULT_CONTROLLER);
	}

	/**
	 * Gets the controller position (as stored in $this->controllerPosition)
	 *
	 * @access protected
	 * @return int
	 */
	protected function getControllerPosition()
	{
		return $this->controllerPosition;
	}

	/**
	 * Public frontend(?) to getMethod. Used so there's no chance of $position being set when getMethod() is called
	 *
	 * @access public
	 * @param string $URI
	 * @return function
	 */
	public function method($URI)
	{
		return $this->getMethod($URI);
	}

	/**
	 * Determines the method from the pattern stored in $this->path and the supplied URI
	 *
	 * @access public
	 * @param string $URI
	 * @param int $position Allows recursion
	 * @return string
	 */
	protected function getMethod($URI, $position = 1)
	{
		$uriParts = explode('/', $URI);
		$controllerPosition = $this->getControllerPosition();

		
		$position = $this->setMethodPosition($position, $controllerPosition);

		/*
		 * If the URI is empty (index), set it to an empty array instead of an array with [0] = ''
		 */
		$uriParts = ($uriParts[0] == '') ? array() : $uriParts;

		if (isset($this->patternParts[$position]))
		{
			$patternPart = $this->patternParts[$position];
		}
		else 
		{
			$this->methodPosition = -1;
			return $this->config->router(self::DEFAULT_METHOD);
		}

		$explicitMethod = $this->returnExplicit('method', $patternPart, $position);
		if ($explicitMethod)
		{
			return $explicitMethod;
		}

		/*
		 * Here, if the method is not explcitly set by the pattern, but rather a {method} is used, the method must be extracted from the URI
		 */
		if ($patternPart == self::METHOD && $position <= count($uriParts) - 1)
		{
			$this->methodPosition = $position;
			return $uriParts[$position];
		}
		elseif ($patternPart == self::IGNORE)
		{
			return $this->getMethod($URI, $position + 1);
		}

		/*
		 * If nothing else matches, return the default method
		 */
		$this->methodPosition = -1;
		return $this->config->router(self::DEFAULT_METHOD);
	}

	/**
	 * Sets the method position to one more than the $controllerPosition
	 *
	 * @access protected
	 * @param int $position
	 * @param int $controllerPosition
	 * @return int
	 */
	protected function setMethodPosition($position, $controllerPosition)
	{
		/*
		 * If the position given in the argument is less than the controller position, increment
		 */
		if ($position <= $controllerPosition)
		{
			$position++;
			setMethodPosition($position, $controllerPosition);
		}
		else
		{
			return $position;
		}
	}

	/**
	 * Gets the method position (as stored in $this->methodPosition)
	 *
	 * @access protected
	 * @return int
	 */
	protected function getMethodPosition()
	{
		return $this->methodPosition;
	}

	/**
	 * Gets the arguments from the path stored in $this->path and the supplied URI
	 *
	 * @access public
	 * @param string $URI
	 * @return array An ordered list containing all the arguments to pass to the dispatcher
	 */
	public function args($URI)
	{
		$uriParts = explode('/', $URI);
		$methodPosition = $this->getMethodPosition();

		/*
		 * Only take the parts of both patternParts and uriParts that correspond to arguments
		 */
		 $patternParts = array_slice($this->patternParts, $methodPosition);
		 $uriParts = array_slice($uriParts, $methodPosition);
				
		return $this->buildArgsArray($patternParts, $uriParts);		
	}

	/**
	 * Builds the arguments array from inputs
	 *
	 * @access protected
	 * @param array $patternParts
	 * @param array $uriParts
	 * @return array An ordered list containing all the arguments to pass to the dispatcher
	 */
	protected function buildArgsArray($patternParts, $uriParts)
	{
		$args = array();
		foreach ($patternParts as $element=>$patternPart)
		{
			/*
			 * If the $patternPart is not enclosed with {}, push it to the end of args[] otherwise, extract the args from uriParts
			 */
			if (preg_match(self::PATTERN_REGEX, $patternPart) != 1)
			{
				$args[] = $patternPart;
			}
			elseif ($patternPart == self::ARGS)
			{
				$args = array_merge($args, array_slice($uriParts, $element));
			}
		}

		return $args;		
	}
}
