<?php 


namespace Serene\Core\Base;

interface Route
{
	public function matches($URI);

	public function controller($URI);

	public function method($URI);

	public function args($URI);
}
