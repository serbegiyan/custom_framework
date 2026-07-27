<?php

namespace App\Core;

use App\Controllers\AnalizeController;
use App\Controllers\GeneratorController;
use App\Controllers\ImporterController;
use App\Interfaces\ContainerInterface;
use App\Controllers\AuthController;
use App\Controllers\OrganizationController;

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
            '/statics' => [AnalizeController::class, 'index'],
            '/organizations' => [OrganizationController::class, 'index'],
        ],
        'POST' => [
            '/statics/imports' => [ImporterController::class, 'store'],
            '/statics/generations' => [GeneratorController::class, 'generate'],
            '/users/login' => [AuthController::class, 'login'],
            '/users/registration' => [AuthController::class, 'register'],
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
            if (is_object($controller) && method_exists($controller, $controllerMethod)) {
                $controller->$controllerMethod();
            } else {
                throw new \RuntimeException("Controller or method not found");
            }
        } else {
            echo 'Page not found(404)';
        }
    }
}
