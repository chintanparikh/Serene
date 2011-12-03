<?php 


namespace Serene\Core\Base;

interface Route
{
	public function matches($URI);

	public function getController($URI, $position);

	public function getMethod($URI, $position);

	public function getArgs($URI);
}
