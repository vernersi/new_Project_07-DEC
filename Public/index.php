<?php
require_once '../vendor/autoload.php';

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
foreach ($authVariables as $variable) {
    $variable = new $variable;
    $twig->addGlobal($variable->getName(), $variable->getValue());
}
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $route) {
    $route->addRoute('GET', '/', ['App\Controllers\IndexController', 'index']);
    $route->addRoute('POST', '/', ['App\Controllers\IndexController', 'login']);
    $route->addRoute('GET', '/register', ['App\Controllers\RegisterController', 'showForm']);
    $route->addRoute('POST', '/register', ['App\Controllers\RegisterController', 'store']);
    $route->addRoute('GET', '/home', ['App\Controllers\HomeController', 'showForm']);
    $route->addRoute('GET', '/logout', ['App\Controllers\LogoutController', 'execute']);
    $route->addRoute('GET', '/home/search', ['App\Controllers\SearchController', 'search']);
    $route->addRoute('POST', '/buy', ['App\Controllers\TradeController', 'buy']);
    $route->addRoute('POST', '/sell', ['App\Controllers\TradeController', 'sell']);
    $route->addRoute('POST', '/short', ['App\Controllers\TradeController', 'short']);
    $route->addRoute('GET', '/profile', ['App\Controllers\ProfileController', 'showForm']);
    $route->addRoute('POST', '/deposit', ['App\Controllers\DepositWithdrawController', 'deposit']);
    $route->addRoute('POST', '/withdraw', ['App\Controllers\DepositWithdrawController', 'withdraw']);
    $route->addRoute('GET', '/profile/viewUser', ['App\Controllers\FriendActionController', 'viewUserProfile']);
    $route->addRoute('POST', '/profile/viewUser', ['App\Controllers\FriendActionController', 'transferStock']);
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

        if ($response instanceof \App\Redirect) {
            header('Location: ' . $response->getUrl());
        }

        break;
}


?>