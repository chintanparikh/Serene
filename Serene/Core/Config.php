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
	/**
	 * Can be 'php', 'ini', 'json' or 'xml'
	 */
	const TYPE = 'PHP';
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
     * Gets the current instance of the class, or initiates the class of no Instance is present (Singleton Design Pattern)
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
     * Lets us do something like $config->database('host'); This would load the file $this->pathToConfig/database.php, and return $config['database']['host'];
     *
     * @access public
     * @param string $configFile The name of the configuration file located in  $this->pathToConfig
     * @param string $property
     * @return string
     */
	public function __call($configFile, $property)
	{
		$config = $this->_loadConfig($configFile);
		if (array_key_exists(0, $property))
		{
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
			return $config[$configFile];
		}
	}

	/**
     * Loads a config file (We can't use the Load Object because Config is always instantiated first, Load depends on Config)
     *
     * @access private
     * @param string $filename The name of the configuration file located in  $this->pathToConfig
     * @return array
     */
	protected function _loadConfig($filename)
	{	
		if (!isset($config[$filename]))
		{
			$path = $this->pathToConfig . $filename . '.' . self::TYPE;
			$function = '_load' . self::TYPE . 'Config';
			return $this->$function($path);
		}
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
	{}
	
}

