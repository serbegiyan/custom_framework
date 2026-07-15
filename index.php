<?php

require_once __DIR__ . '/autoload.php';

use Core\Database;
use Core\Router;
use Core\Request;

$db = new Database();
$pdo = $db->connect();
$request = new Request();

$router = new Router($pdo, $request);
$router->dispatch();