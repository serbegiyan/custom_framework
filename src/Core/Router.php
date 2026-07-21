<?php

namespace App\Core;

use App\Controllers\AnalizeController;
use App\Controllers\GeneratorController;
use App\Controllers\ImporterController;
use App\Core\Interfaces\ContainerInterface;

class Router
{
    public function __construct(
        public ContainerInterface $container
    ) {
    }

    /**
    * @var array<string, array<string, array{0: string, 1:string}>>
    */
    private array $routes = [
        'GET' => [
            '/users' => [AnalizeController::class, 'index'],
        ],
        'POST' => [
            '/users/imports' => [ImporterController::class, 'store'],
            '/users/generations' => [GeneratorController::class, 'generate'],
        ]
    ];

    public function dispatch(): void
    {
        $request = $this->container->get(Request::class);
        $path = $request->getPath();
        $method = $request->getMethod();

        if (array_key_exists($method, $this->routes) and array_key_exists($path, $this->routes[$method])) {
            $controllerClass = $this->routes[$method][$path][0];
            $controllerMethod = $this->routes[$method][$path][1];
            $controller = $this->container->get($controllerClass);
            $controller->$controllerMethod();
        } else {
            echo 'Page not found(404)';
        }
    }
}
