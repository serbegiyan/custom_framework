<?php

namespace App\Core;

use App\Controllers\AnalizeController;
use App\Controllers\GeneratorController;
use App\Controllers\ImporterController;

class Router
{
    public function __construct(
        public \PDO $pdo,
        public Request $request
    )
    {}
    private array $routes = [
        '/import' => [ImporterController::class, 'store'],
        '/analize' => [AnalizeController::class, 'index'],
        '/generate' => [GeneratorController::class, 'generate'],
    ];

    public function dispatch()
    {
        $path = $this->request->getPath();
        if(array_key_exists($path, $this->routes)){
            $controllerClass = $this->routes[$path][0];
            $method = $this->routes[$path][1];
            $controller = new $controllerClass($this->pdo, $this->request);
            $controller->$method();
        }else{
            echo 'Page not found(404)';
            return http_response_code(404);
        }
    }
}