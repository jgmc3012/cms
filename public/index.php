<?php
ini_set('display_errors', 1);
ini_set('display_starup_error', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

session_start();

use Middlewares\AuraRouter;
use WoohooLabs\Harmony\Harmony;
use WoohooLabs\Harmony\Middleware\DispatcherMiddleware;
use WoohooLabs\Harmony\Middleware\HttpHandlerRunnerMiddleware;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Aura\Router\RouterContainer;
use Zend\Diactoros\Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Middlewares\AuthenticationMiddleware;
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

$map->get('login', '/login-cms', [
  'App\Controllers\AuthController',
  'loginRender',
]);

$map->post('login_user', '/login-cms', [
  'App\Controllers\AuthController',
  'loginUser',
]);

$map->get('logout_user', '/logout-cms', [
    'App\Controllers\AuthController',
    'logoutUser',
]);

$map->get('posts', '/post', [
  'App\Controllers\PostController',
  'postAction',
]);

$map->get('post.layout', '/post-layout', [
    'App\Controllers\PostController',
    'postLayout',
]);

$map->get('user.show', '/dashboard/users', [
  'App\Controllers\UserController',
  'showUsers',
]);

$map->post('user.add', '/dashboard/users/add', [
  'App\Controllers\UserController',
  'addUser',
]);

$map->get('user.act-rm', '/dashboard/users/act-rm/{id}', [
  'App\Controllers\UserController',
  'activeRemoveUser',
]);

$map->get('overview', '/dashboard/overview', [
  'App\Controllers\DashboardController',
  'overviewAction',
]);

$map->get('category.show', '/dashboard/category', [
  'App\Controllers\CategoryController',
  'showCategories',
]);

$map->post('category.add', '/dashboard/category/add', [
  'App\Controllers\CategoryController',
  'addCategory',
]);

$map->get('Category.act-rm', '/dashboard/category/act-rm/{id}', [
    'App\Controllers\CategoryController',
    'activeRemoveCategory',
]);

$map->get('post.new', '/dashboard/post/new-post', [
  'App\Controllers\PostController',
  'newPost',
]);

$map->get('post.dashboard', '/dashboard/post', [
    'App\Controllers\PostController',
    'dashboardPost',
]);

$map->get('index', '/', [
    'App\Controllers\IndexController',
    'dashboardPost',
]);

$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

try {
  $harmony = new Harmony($request, new Response());
  $harmony
  ->addMiddleware(new HttpHandlerRunnerMiddleware(new SapiEmitter()))
  ->addMiddleware(new AuthenticationMiddleware())
  ->addMiddleware(new \App\Middlewares\AccessPermitsMiddleware())
  ->addMiddleware(new Middlewares\AuraRouter($routerContainer))
  ->addMiddleware(new DispatcherMiddleware($container,'request-handler'))

  ->run();

} catch (Exception $e) {
    switch ($e->getCode()){
        case 401:
            if ($e->getMessage() == 'Usuario no logueado'){
                ob_start();
                header('Location: /login-cms');
            };
            break;
        default:
            var_dump($e);
            $emitter = new SapiEmitter();
            $emitter->emit(new Response\EmptyResponse(500));
            break;
    };
}