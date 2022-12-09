<?php
require_once '../vendor/autoload.php';
use App\RequestFromApi;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$dotenv = Dotenv\Dotenv::createImmutable('../');
$dotenv->load();
session_start();
$loader = new FilesystemLoader('../views');
$twig = new Environment($loader);

$authVariables = [
    \App\ViewVariables\AuthViewVariables::class,
    \App\ViewVariables\ErrorsViewVariables::class
];
foreach ($authVariables as $variable){
    $variable = new $variable;
    $twig->addGlobal($variable->getName(), $variable->getValue());
}
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $route) {
    $route->addRoute('GET', '/', ['App\Controllers\IndexController', 'index']);
    $route->addRoute('POST', '/', ['App\Controllers\IndexController', 'login']);
    $route->addRoute('GET', '/register', ['App\Controllers\RegisterController', 'showForm']);
    $route->addRoute('GET', '/profile', ['App\Controllers\ProfileController', 'showForm']);
    //$route->addRoute('GET', '/login', ['App\Controllers\LoginController', 'showForm']);
    //$route->addRoute('POST', '/login', ['App\Controllers\LoginController', 'login']);
    //$route->addRoute('GET', '/profile', ['App\Controllers\ProfileController', 'showForm']);
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        [$controller, $method] = $handler;
        $response = (new $controller)->{$method}();
        if ($response instanceof \App\Template) {
            echo $twig->render($response->getPath(), $response->getParams());
            unset($_SESSION['errors']);
        }

        if ($response instanceof \App\Redirect){
            header('Location: '.$response->getUrl());
        }

        break;
}


?>