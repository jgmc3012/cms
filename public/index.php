<?php
ini_set('display_errors', 1);
ini_set('display_starup_error', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

use Aura\Router\RouterContainer;
use Zend\Diactoros\ServerRequestFactory;
use App\Controllers\BaseController;

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);
$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

$map->get('index', '/post', [
    'action' => 'PostAction',
    'controller' =>'App\Controllers\PostController',
]);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

if (!$route) {
    echo 'No route';
} else {
  $handlerData = $route->handler;
  $controllerName = $handlerData['controller'];
  $actionName = $handlerData['action'];

  $controller = new $controllerName;
  $response = $controller->$actionName($request);

  echo $response;
}
