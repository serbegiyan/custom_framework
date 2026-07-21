<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Container;
use App\Core\Request;
use App\Core\Database;
use App\Core\Interfaces\ContainerInterface;
use App\Core\Router;

$container = new Container();
$container->set(Request::class, function (ContainerInterface $c){
    return new Request;
});
$container->set(PDO::class, function(ContainerInterface $c){
    $db = new Database();
    $pdo = $db->connect();
    return $pdo;
});
$container->set(Router::class, function (ContainerInterface $c){
    $router = new Router($c);
    return $router;
});
$router = $container->get(Router::class);
$router->dispatch();

