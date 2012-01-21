<?php
/**
 * This class is used to manage the configuration.
 *
 * @version 1.0
 * @author timtamboy63 (timtamboy63@gmail.com)
 * @license Creative Commons
 *
 * Changelog:
 *
 */

namespace Serene\Core;

class Config
{
	const DEFAULT_TYPE = 'php';
	/**
	 * Acceptable types of config files.
	 * 
	 * @var array
	 */
	protected $types = array('php', 'ini', 'json', 'xml');
	/**
     * Holds the Config instance
     *
     * @var Config
     */
	private static $_instance;

	/**
	 * Holds the path to all config files
	 *
	 * @var string
	 */
	protected $pathToConfig;

	/**
	 * Holds the cache of all config values accessed
	 * 
	 * @var array
	 */
	protected $configCache;

	/**
     * Gets the current instance of the class, or initiates the class if no Instance is present (Singleton Design Pattern)
     *
     * @access public
     * @return Config
     */
	public function getInstance($path = 'Serene/Config/')
	{
		if (empty(self::$_instance))
		{
			self::$_instance = new Config($path);
		}

		return self::$_instance;
	}

	protected function __construct($path)
	{
		$this->pathToConfig = $path;
	}

	/**
     * Allows the function name to be used as a string to load the config file.
     * Lets us do something like $config->database('host', 'xml'); This would load the file $this->pathToConfig/database.xml, and return $config['database']['host'];
     *
     * @access public
     * @param string $configFile The name of the configuration file located in  $this->pathToConfig
     * @param array $property [$configFile, ($type)]. If $type is not set, it is set to 'php' by default
     * @return string
     */
	public function __call($configFile, $property)
	{
		// default type is a magic constant
		$type = self::DEFAULT_TYPE;
		// overrides default if the type is set in the class (eg $config->database('host', 'xml');
		if (array_key_exists(1, $property))
		{
			$type = $property[1];
			if (!in_array($property[1], $this->types))
			{
				throw new \Exception('Type of config file not available');
				$type = DEFAULT_TYPE;	
			}
		}

		
		
		if (array_key_exists(0, $property))
		{
			if (!$this->_inCache($configFile, $property[0]))
			{
				$config = $this->_loadConfig($configFile, $type);
				$this->_addToCache($config);
				if (is_array($config[$configFile]))
				{
					return $config[$configFile][$property[0]];
				}
				else
				{
					throw new \Exception('Config file is not formatted properly');
				}	
			}
			else
			{
				return $this->_readCache($configFile, $property[0]);
			}
			
		}
		else
		{
			return $config[$configFile];
		}
	}

	/**
     * Loads a config file (We can't use the Load Object because Config is always instantiated first, Load depends on Config)
     *
     * @access protected
     * @param string $filename The name of the configuration file located in  $this->pathToConfig
     * @param string $type The type of file we are using
     * @return array
     */
	protected function _loadConfig($filename, $type)
	{	
		if (!isset($config[$filename]))
		{
			$path = $this->pathToConfig . $filename . '.' . $type;
			$function = '_load' . $type . 'Config';
			return $this->$function($path);
		}
	}

	/**
	 * Adds to the cache
	 * 
	 * @access protected
	 * @param array $config The configuration to be merged with the existing cache
	 * @return null
	 */
	protected function _addToCache($config)
	{
		// typecasted $this->configCache as array because it isn't an array when the cache is empty
		$this->configCache = array_merge((array)$this->configCache, $config);
	}

	/**
	 * Checks whether a property is in the cache
	 * 
	 * @access protected
	 * @param string $configFile The filename of the file where the property is stored in
	 * @param string $property The name of the property to be checked
	 * @return bool
	 */
	protected function _inCache($configFile, $property)
	{
		if (isset($this->configCache[$configFile][$property]))
		{
			return true;
		}
		return false;
	}

	/**
	 * Returns a property from the cache
	 * 
	 * @access protected
	 * @param string $configFile The filename of the file where the property is stored in
	 * @param string $property The name of the property to be accessed
	 * @return mixed
	 */
	protected function _readCache($configFile, $property)
	{
		return $this->configCache[$configFile][$property];
	}

	private function _loadPHPConfig($path)
	{
		if (file_exists($path))
		{
			require($path);
		}
		else
		{
			throw new \Exception("Config file {$path} does not exist!");
		}
		/*
		 * $config is the array found in ALL PHP  config files stored in $this->pathToConfig/
		 */
		return $config;
	}

	private function _loadINIConfig($path)
	{
		if (file_exists($path))
		{
			$config = parse_ini_file($path, true);
		}
		else
		{
			throw new \Exception("Config file {$path} does not exist!"); 
		}
		return $config;
	}

	private function _loadXMLConfig($path)
	{
		if (file_exists($path))
		{
			$raw = file_get_contents($path);
			$xml = new SimpleXMLElement($raw);

			$vars = get_object_vars($xml);
			foreach ($vars as $key=>$value)
			{
				$name = $key;
			}
			
			// Remove the comments field (uncessary, and we don't need it))
			unset($xml->$name->comment);
			$config[$name] = get_object_vars($xml->$name);
		}
		else
		{
			throw new \Exception("Config file {$path} does not exist!"); 
		}

		return $config;
	}

	private function _loadJSONConfig($path)
	{
		if (file_exists($path))
		{
			$raw = file_get_contents($path);
			$config = json_decode($file, true);
		}
		else
		{
			throw new \Exception("Config file {$path} does not exist!"); 
		}

		return $config;
	}
	
}

