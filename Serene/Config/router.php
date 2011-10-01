<?php

/**
* The default controller to call on the index
*/
$config['router']['defaultController'] = 'controller';
/**
* The default method to call when no method is specified
*/
$config['router']['defaultMethod'] = 'index';
/**
* The default routing pattern
*/
$config['router']['defaultRoutingPattern'] = '{controller}/{method}/{args}';
/**
* The directly your index.php is stored in. Must NOT be followed by a '/'
*/
$config['router']['path'] = 'Serene';

?>