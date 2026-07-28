<?php

namespace App\Core;

use App\Controllers\AnalizeController;
use App\Controllers\AuthController;
use App\Controllers\GeneratorController;
use App\Controllers\ImporterController;
use App\Controllers\LanguageController;
use App\Controllers\OrganizationController;
use App\Interfaces\ContainerInterface;
use App\Interfaces\MiddlewareInterface;
use App\Middlewares\AuthMiddleware;

class Router
{
    public function __construct(
        public ContainerInterface $container,
    ) {
    }

    /**
    * @var array<string, array<string, array{controller: string, method: string, middleware: array<int, string>}>>
    */
    private array $routes = [
        'GET' => [
            '/statics' => [
                'controller' => AnalizeController::class,
                'method' => 'index',
                'middleware' => [],
            ],
            '/organizations' => [
                'controller' => OrganizationController::class,
                'method' => 'index',
                'middleware' => [AuthMiddleware::class],
            ],
        ],
        'POST' => [
            '/statics/imports' => [
                'controller' => ImporterController::class,
                'method' => 'store',
                'middleware' => [],
            ],
            '/statics/generations' => [
                'controller' => GeneratorController::class,
                'method' => 'generate',
                'middleware' => [],
            ],
            '/users/login' => [
                'controller' => AuthController::class,
                'method' => 'login',
                'middleware' => [],
            ],
            '/users/registration' => [
                'controller' => AuthController::class,
                'method' => 'register',
                'middleware' => [],
            ],
            '/users/logout' => [
                'controller' => AuthController::class,
                'method' => 'logout',
                'middleware' => [],
            ],
            '/lang/switch' => [
                'controller' => LanguageController::class,
                'method' => 'switchLang',
                'middleware' => [],
            ],
        ]
    ];

    public function dispatch(): void
    {
        $request = $this->container->get(Request::class);
        $session = $this->container->get(Session::class);
        $path = $request->getPath();
        $method = $request->getMethod();

        if (array_key_exists($method, $this->routes) and array_key_exists($path, $this->routes[$method])) {
            $controllerClass = $this->routes[$method][$path]['controller'];
            $controllerMethod = $this->routes[$method][$path]['method'];
            $middlewareClasses = $this->routes[$method][$path]['middleware'];

            foreach ($middlewareClasses as $middlewareClass) {
                $middleware = $this->container->get($middlewareClass);
                if ($middleware instanceof MiddlewareInterface) {
                    $middleware->handle($request, $session);
                }
            }

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
