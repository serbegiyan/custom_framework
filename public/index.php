<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Core\Request;
use App\Core\Router;

$db = new Database();
$pdo = $db->connect();
$request = new Request();

$router = new Router($pdo, $request);
$router->dispatch();
