<?php
/**
 * Controller for the 404 error page
 */

class error404 extends Application
{
	
	function __construct()
	{
		Load::view('Errors/404');
	}
}


?>