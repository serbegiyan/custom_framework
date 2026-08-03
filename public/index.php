<?php

require __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->load();

use App\Core\Container;
use App\Core\Database;
use App\Core\Request;
use App\Core\Router;
use App\Interfaces\ContainerInterface;
use App\Interfaces\DatabaseInterface;

$container = new Container();
$container->set(ContainerInterface::class, function (ContainerInterface $c) {
    return $c;
});
$container->set(DatabaseInterface::class, function (ContainerInterface $c) {
    $dsn = $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];

    return new Database($dsn, $user, $password);
});

$router = $container->get(Router::class);

require __DIR__ . '/../routes/web.php';

$router->dispatch();
