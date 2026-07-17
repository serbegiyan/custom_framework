<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Core\Router;
use App\Core\Request;

$db = new Database();
$pdo = $db->connect();
$request = new Request();

$router = new Router($pdo, $request);
$router->dispatch();