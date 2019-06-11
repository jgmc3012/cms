<?php
ini_set('display_errors', 1);
ini_set('display_starup_error', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

use Aura\Router\RouterContainer;
use Zend\Diactoros\ServerRequestFactory;
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'port'      => '3307',
    'database'  => 'uniremin',
    'username'  => 'root',
    'password'  => 'root',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);
$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

$map->get('posts', '/post', [
    'action' => 'postAction',
    'controller' =>'App\Controllers\PostController',
]);

$map->get('newuser', '/adduser', [
    'action' => 'addUser',
    'controller' =>'App\Controllers\UserController',
]);

$map->get('showuser', '/dashboard/users', [
    'action' => 'showUsers',
    'controller' =>'App\Controllers\UserController',
]);

$map->post('addwuser', '/dashboard/add-user', [
    'action' => 'addUser',
    'controller' =>'App\Controllers\UserController',
]);

$map->get('user.rm', '/dashboard/rm-user/', [
    'action' => 'rmUser',
    'controller' =>'App\Controllers\UserController',
]);

$map->get('overview', '/dashboard/overview', [
    'action' => 'overviewAction',
    'controller' =>'App\Controllers\DashboardController',
]);

$map->get('category', '/dashboard/category', [
    'action' => 'showCategories',
    'controller' =>'App\Controllers\CategoryController',
]);

$map->post('addCategory', '/dashboard/add-category', [
    'action' => 'addCategory',
    'controller' =>'App\Controllers\CategoryController',
]);

$map->get('post.new', '/dashboard/new-post', [
    'action' => 'newPost',
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
