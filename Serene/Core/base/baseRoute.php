<?php 

interface BaseRoute
{
	public function matches($URI);

	public function getController($URI, $position);

	public function getMethod($URI, $position);

	public function getArgs($URI);
}

?>