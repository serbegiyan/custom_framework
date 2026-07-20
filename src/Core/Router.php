<?php

namespace App\Core;

use App\Controllers\AnalizeController;
use App\Controllers\GeneratorController;
use App\Controllers\ImporterController;
use PDO;

class Router
{
    public function __construct(
        public PDO $pdo,
        public Request $request
    ) {
    }

    /**
    * @var array<string, array<int, string>>
    */
    private array $routes = [
        '/import' => [ImporterController::class, 'store'],
        '/analize' => [AnalizeController::class, 'index'],
        '/generate' => [GeneratorController::class, 'generate'],
    ];

    public function dispatch(): void
    {
        $path = $this->request->getPath();
        if (array_key_exists($path, $this->routes)) {
            $controllerClass = $this->routes[$path][0];
            $method = $this->routes[$path][1];
            $controller = new $controllerClass($this->pdo, $this->request);
            $controller->$method();
        } else {
            echo 'Page not found(404)';
        }
    }
}
