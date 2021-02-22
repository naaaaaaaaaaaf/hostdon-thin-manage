<?php

use Dotenv\Dotenv;

require_once 'vendor/autoload.php';
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dispatcher= FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $router) {
    //$router->addRoute(['GET','POST'], '/', 'Hostdon\TestController::index');
    $router->addRoute(['GET'], '/registration', 'Hostdon\RegistrationController::index');
    $router->addRoute(['POST'], '/registration', 'Hostdon\RegistrationController::email_post');

});

// HTTPメソッドとUILを取得する
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
        header('HTTP/1.0 404 Not Found');
        echo not_found();
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        header('HTTP/1.0 405 Method Not Allowed');
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $handler($vars);
        break;
}

function not_found()
{
    return 'Not Foundです';
}