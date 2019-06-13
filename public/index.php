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

$map->get('newuser', '/dashboard/adduser', [
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

$map->get('user.active', '/dashboard/act-rm-user/{id}', [
  'App\Controllers\UserController',
  'activeUser',
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

$map->get('Category.act-rm', '/dashboard/act-rm-category/{id}', [
    'App\Controllers\CategoryController',
    'active_remove_Category',
]);

$map->get('post.new', '/dashboard/new-post', [
  'App\Controllers\PostController',
  'newPost',
]);

$map->get('post.dashboard', '/dashboard/post', [
    'App\Controllers\PostController',
    'dashboardPost',
]);
$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

try {
  $harmony = new Harmony($request, new Response());
  $harmony
  ->addMiddleware(new HttpHandlerRunnerMiddleware(new SapiEmitter()))
  ->addMiddleware(new Middlewares\AuraRouter($routerContainer))
  ->addMiddleware(new AuthenticationMiddleware())
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
} catch (Error $e) {
    echo 'ERROR <br/>';
    echo var_dump($e);
}
