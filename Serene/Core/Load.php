<?php

/**
 * A class to load other objects, etc
 *
 * @version 1.0
 * @author timtamboy63 (timtamboy63@gmail.com)
 * @license Creative Commons
 *
 * Todo:
 # Throw exceptions in the Load class instead of the Dispatcher
 *
 */

namespace Serene\Core;

class Load
{
	/**
	 * Holds the config instance
	 *
	 * @var Config
	 */
	protected $config;

	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	/**
     * Loads a controller from the path stored in the config.
     *
     * @access public
     * @param string $filename The name of the controller located in  Application/Controllers
     * @return Object
     */
	public function controller($filename)
	{
		if (file_exists( $this->config->load('controllerPath') . '/' . $filename . '.php' ))
		{
			require_once $this->config->load('controllerPath') . '/' . $filename . '.php';
			$class = '\\Serene\\Controllers\\' . $filename;
			return new $class($this, $this->config);
		}

		return false;
	}

	/**
     * Loads a model from the path stored in the config.
     *
     * @access public
     * @param string $filename The name of the controller located in  Application/Models
     * @return Object
     */
	public function model($filename)
	{
		require_once $this->config->load('modelPath') . '/' . $filename . '.php';
		$model = '\\Serene\\Model\\' . $filename; 
		return new $model;
	}

	/**
     * Loads a view from the path stored in the config, and passes it data
     *
     * @access public
     * @param string $filename The name of the controller located in  Application/Controllers
     * @param array $data The data to be passed through to the view - it gets extracted, so $array['foo'] = 'bar'; becomes $foo = 'bar';
     */
	public function view($filename, $data = NULL)
	{
		if (is_array($data))
		{
			extract($data);
		}
		ob_start();
		require_once $this->config->load('viewPath') . '/' . $filename . '.php'; 
		ob_end_flush();
	}

	/**
     * Loads a library from the path stored in the config.
     *
     * @access public
     * @param string $filename The name of the controller located in  Application/Libraries
     */
	public function library($filename)
	{
		require_once $this->config->load('libraryPath') . '/' . $filename . '.class.php';
	}

	/**
     * Loads an error from the path stored in the config. Use it for 404, 403 errors, etc. Keep in mind that 404 errors must be called manually, as by default they will redirect to /index.php
     *
     * @access public
     * @param string $filename The name of the error located in  Application/Errors
     */
	public function error($filename)
	{
		if (file_exists($this->config->load('errorPath') . '/' . $filename . '.php'))
		{
			include_once $this->config->load('errorPath') . '/' . $filename . '.php';
			$name = 'error' . $filename;
			return new $name;
		}
		return false;
	}



}

