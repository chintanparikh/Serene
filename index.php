<?php 
use Serene\Core\Exception as SereneException;
define("BASE_PATH", "Serene");
require BASE_PATH . '/base.php';

$application = new Serene\Application();
$application->start();
