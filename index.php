<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require_once('vendor/autoload.php');
require_once('routes.php');

$appContainer = new \App\Support\AppContainer();
$resolver = new \App\Support\RouteResolver($appContainer->getInstance());
$dispatcher = new Phroute\Phroute\Dispatcher($routes->getData(), $resolver);

$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
echo $response;