<?php
ini_set('display_errors', 1);
ini_set('display_starup_error', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

session_start();

use WoohooLabs\Harmony\Harmony;
use WoohooLabs\Harmony\Middleware\DispatcherMiddleware;
use WoohooLabs\Harmony\Middleware\HttpHandlerRunnerMiddleware;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Aura\Router\RouterContainer;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
use Illuminate\Database\Capsule\Manager as Capsule;

$container = new DI\Container();

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

$map->get('login', '/dashboard/login', [
  'App\Controllers\AuthController',
  'loginRender',
]);

$map->post('login.user', '/dashboard/login', [
    'App\Controllers\AuthController',
     'loginUser',
]);

$map->get('posts', '/post', [
    'App\Controllers\PostController',
     'postAction',
]);

$map->get('newuser', '/adduser', [
    'App\Controllers\UserController',
     'addUser',
]);

$map->get('showuser', '/dashboard/users', [
    'App\Controllers\UserController',
     'showUsers',
]);

$map->post('addwuser', '/dashboard/add-user', [
    'App\Controllers\UserController',
     'addUser',
]);

$map->get('user.rm', '/dashboard/rm-user/', [
    'App\Controllers\UserController',
     'rmUser',
]);

$map->get('overview', '/dashboard/overview', [
    'App\Controllers\DashboardController',
     'overviewAction',
]);

$map->get('category', '/dashboard/category', [
    'App\Controllers\CategoryController',
     'showCategories',
]);

$map->post('addCategory', '/dashboard/add-category', [
    'App\Controllers\CategoryController',
     'addCategory',
]);

$map->get('post.new', '/dashboard/new-post', [
    'App\Controllers\PostController',
     'newPost',
]);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

try {
  $harmony = new Harmony($request, new Response());
  $harmony
  ->addMiddleware(new HttpHandlerRunnerMiddleware(new SapiEmitter()))
  ->addMiddleware(new Middlewares\AuraRouter($routerContainer))
  ->addMiddleware(new DispatcherMiddleware($container,'request-handler'))
  ->run();

} catch (\Exception $e) {

}


/*
if (!$route) {
echo 'No route';
} else {
$handlerData = $route->handler;
$controllerName = $handlerData['controller'];
$actionName = $handlerData['action'];
  $controller = new $controllerName;
  $response = $controller->$actionName($request);

  foreach($response->getHeaders() as $name => $values)
  {
      foreach($values as $value) {
          header(sprintf('%s: %s', $name, $value), false);
      }
  }
  http_response_code($response->getStatusCode());
  echo $response->getBody();
}
*/
