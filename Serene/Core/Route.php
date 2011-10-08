<?php 
/**
 * Route Class - Creates Routes to be used by the Router
 *
 * Special values:
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
	 * The path that the Route will apply for
	 *
	 * @var string
	 */
	public $path;

	/**
	 * The routing pattern that determines the route
	 *
	 * @var string
	 */
	public $pattern;

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

	public function __construct($path, $pattern)
	{
		$this->path = strtolower($path);
		$this->pattern = strtolower($pattern);
		$this->config = Config::getInstance();
	}

	protected function validate()
	{
		
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
		/*
		 * Create Arrays of both the Path and URI, for each segment.
		 */
		$pathParts = explode('/', $this->path);
		$uriParts = array('');
		if (empty($URI))
		{
			$uriParts = explode('/', $URI, count($pathParts));
		}
		
		/*
		 * If the condition is true, then the path cannot match the URI, because there are more segments to the path than the URI. For example, $path = blog/view/{*}; $URI = blog
		 * In other worse, the path is more specific than the URI.
		 * It is count() - 1 to take into account the possibility of {*}
		 */
		if (count($pathParts) - 1 > count($uriParts))
		{
			return false;
		}

		$element = -1;
		foreach ($pathParts as $pathPart)
		{
			$element++;
			/*
			 * If the segment = {*}, then we know the rest of the string matches, because {*} matches anything, and must come last
			 */
			if ($pathPart == '{*}')
			{
				return true;
			}
			/*
			 * If we are here, $pathPart is a string. Thus, if the string matches the corresponding $uriPart, then we move on the the next segment, otherwise return false 
			 */
			else
			{
				if ($pathPart != $uriParts[$element])
				{
					return false;
				}
			}
		}
	}

	/**
	 * Determines the controller from the pattern stored in $this->path and the supplied URI
	 *
	 * @access public
	 * @param string $URI
	 * @param int $position This should never be set when getController() is called, it is simply there so that recursion works
	 * @return string
	 */
	public function getController($URI, $position = 0)
	{
		$patternParts = explode('/', $this->pattern);
		$uriParts = explode('/', $URI);

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
			$this->controllerPosition = -1;
			return $this->config->router('defaultController');
		}

		/*
		 * If $pathPart is not enclosed in {} (i.e it is a string),
		 * Check if {controller} also exists in $pathParts - if so, the Pattern has not been created properly
		 * If not, return that string as the controller
		 */
		if (preg_match('~\{[a-x0-9]+?\}~', $patternPart) != 1)
		{
			if (array_search('{controller}', $patternParts) != false)
			{
				throw new Exception('Both a string and parameter controller have been set in the Pattern');
			}
			else
			{
				$this->controllerPosition = $position;
				return $patternPart;
			}
		}
		/*
		 * Here, if the controller is not explcitly set by the pattern, but rather a {controller} is used, and the controller must be extracted from the URI
		 */
		elseif ($patternPart == '{controller}' && $position <= count($uriParts) - 1)
		{
			$this->controllerPosition = $position;
			return $uriParts[$position];
		}
		/*
		 * Here, if {0} is used, skip to the next part of the string
		 */
		elseif ($patternPart == '{0}')
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

	/**
	 * Determines the method from the pattern stored in $this->path and the supplied URI
	 *
	 * @access public
	 * @param string $URI
	 * @param int $position This should never be set when getController() is called, it is simply there so that recursion works
	 * @return string
	 */
	public function getMethod($URI, $position = 1)
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
		if (preg_match('~\{[a-x0-9]+?\}~', $patternPart) != 1)
		{
			if (array_search('{method}', $patternParts) != false)
			{
				throw new Exception('Both a string and parameter method have been set in the Pattern');
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
		elseif ($patternPart == '{method}' && $position <= count($uriParts) - 1)
		{
			$this->methodPosition = $position;
			return $uriParts[$position];
		}

		/*
		 * Here, if {0} is used, skip to the next part of the string
		 */
		elseif ($patternPart == '{0}')
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
	public function getArgs($URI)
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
			if (preg_match('~\{[a-x0-9]+?\}~', $patternPart) != 1)
			{
				$args[] = $patternPart;
			}
			/*
			 * Otherwise, extract the args from uriParts
			 */
			elseif ($patternPart == '{args}')
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

?>