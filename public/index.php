<?php

require '../vendor/autoload.php';


$router = new Core\Router();

//Error handling
error_reporting(E_ALL);
// set_error_handler('Core\Error::errorHandler');
// set_exception_handler('Core\Error::exceptionHandler');

session_start();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('search', ['controller' => 'Search', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{action}/{id:\d+}');


$router->dispatch($_SERVER['QUERY_STRING']);
?>
