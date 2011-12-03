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
 */

namespace Serene\Core;

use Serene\Core\Base as Base;

class Route implements Base\Route
{
	/**
	 * Constants for the patterns, paths, routes.
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
	 * Render's the path so it can be properly searched.
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

	protected function validate()
	{
		
	}

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

		$element = 0;
		foreach ($this->pathParts as $pathPart)
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

			$element++;
		}
	}

	protected function controllerSegmentExistsInPath()
	{
		return array_key_exists(self::CONTROLLER, $this->patternParts);
	}

	protected function returnExplicitController($patternPart, $position)
	{
		if (preg_match(self::PATTERN_REGEX, $patternPart) != 1)
		{
			if ($this->controllerSegmentExistsInPath())
			{
				throw new \Exception('Both a string and parameter controller have been set in the Pattern');
				return false;
			}
			else
			{
				$this->controllerPosition = $position;
				return $patternPart;
			}
		}
	}

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
			return $this->config->router('defaultController');
		}

		$explicitController = $this->returnExplicitController($patternPart, $position);
		if ($explicitController)
		{
			return $explicitController;
		}
		/*
		 * Here, if the controller is not explcitly set by the pattern, but rather a {controller} is used, and the controller must be extracted from the URI
		 */
		if ($patternPart == self::CONTROLLER && $position <= count($uriParts) - 1)
		{
			$this->controllerPosition = $position;
			return $uriParts[$position];
		}
		/*
		 * Here, if {0} is used, skip to the next part of the string
		 */
		elseif ($patternPart == self::IGNORE)
		{
			return $this->getController($URI, $position + 1);
		}

		/*
		 * If nothing else matches, return the default controller
		 */
		$this->controllerPosition = -1;
		return $this->config->router('defaultController');
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
		$patternParts = explode('/', $this->pattern);
		$uriParts = explode('/', $URI);
		$controllerPosition = $this->getControllerPosition();

		/*
		 * If the position given in the argument is less than the controller position, set it to one more than the controllers position
		 */
		if ($position <= $controllerPosition)
		{
			$position++;
		}

		/*
		 * If the URI is empty (index), set it to an empty array instead of an array with [0] = ''
		 */
		$uriParts = ($uriParts[0] == '') ? array() : $uriParts;

		if (isset($patternParts[$position]))
		{
			$patternPart = $patternParts[$position];
		}
		else 
		{
			$this->methodPosition = -1;
			return $this->config->router('defaultMethod');
		}

		/*
		 * If $pathPart is not enclosed in {} (i.e it is a string),
		 * Check if {method} also exists in $pathParts - if so, the Pattern has not been created properly
		 * If not, return that string as the controller
		 */
		if (preg_match(self::PATTERN_REGEX, $patternPart) != 1)
		{
			if (array_search(self::METHOD, $patternParts) != false)
			{
				throw new \Exception('Both a string and parameter method have been set in the Pattern');
			}
			else
			{
				$this->methodPosition = $position;
				return $patternPart;
			}
		}

		/*
		 * Here, if the method is not explcitly set by the pattern, but rather a {method} is used, and the method must be extracted from the URI
		 */
		elseif ($patternPart == self::METHOD && $position <= count($uriParts) - 1)
		{
			$this->methodPosition = $position;
			return $uriParts[$position];
		}

		/*
		 * Here, if {0} is used, skip to the next part of the string
		 */
		elseif ($patternPart == self::IGNORE)
		{
			return $this->getMethod($URI, $position + 1);
		}

		/*
		 * If nothing else matches, return the default method
		 */
		$this->methodPosition = -1;
		return $this->config->router('defaultMethod');
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
	 * @return array
	 */
	public function args($URI)
	{
		$patternParts = explode('/', $this->pattern);
		$uriParts = explode('/', $URI);
		$methodPosition = $this->getMethodPosition();

		/*
		 * Remove elements off the front? of $patternParts and $uriParts until both the controller and method elements are removed, leaving us with only the arguments
		 */
		$element = 0;
		while ($methodPosition >= $element)
		{
			array_shift($patternParts);
			array_shift($uriParts);
			$element++;
		}
		
		/*
		 * Build the args array
		 */
		$args = array();
		$patternPosition = 0;
		foreach ($patternParts as $patternPart)
		{
			/*
			 * If the $patternPart is not enclosed with {}, push it to the end of args[]
			 */
			if (preg_match(self::PATTERN_REGEX, $patternPart) != 1)
			{
				$args[] = $patternPart;
			}
			/*
			 * Otherwise, extract the args from uriParts
			 */
			elseif ($patternPart == self::ARGS)
			{
				$uriPosition = 0;
				while ($patternPosition > $uriPosition)
				{
					array_shift($uriParts);
					$uriPosition++;
				}
				foreach ($uriParts as $uriPart)
				{
					$args[] = $uriPart;
				}
			}
			$patternPosition++;
		}


		return $args;
	}
}
