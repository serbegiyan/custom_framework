<?php

require __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->load();

use App\Core\Container;
use App\Core\Database;
use App\Core\Interfaces\ContainerInterface;
use App\Core\Interfaces\DatabaseInterface;
use App\Core\Request;
use App\Core\Router;

$container = new Container();
$container->set(DatabaseInterface::class, function (ContainerInterface $c) {
    $dsn = $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];

    return new Database($dsn, $user, $password);
});
$container->set(Request::class, function (ContainerInterface $c) {
    return new Request();
});
$container->set(PDO::class, function (ContainerInterface $c) {
    $db = new Database();
    $pdo = $db->connect();
    return $pdo;
});
$container->set(Router::class, function (ContainerInterface $c) {
    $router = new Router($c);
    return $router;
});
$router = $container->get(Router::class);
$router->dispatch();
