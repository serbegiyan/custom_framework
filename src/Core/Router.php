<?php

namespace Core;

use App\Exceptions\InternalServerErrorException;
use App\Interfaces\ContainerInterface;
use App\Interfaces\MiddlewareInterface;
use App\Interfaces\ResponseInterface;

class Router
{
    public function __construct(
        public ContainerInterface $container,
    ) {
    }

    public string $currentPrefix = '';
    /**
     * @var array<int, string>
     */
    public array $currentMiddlewares = [];

    /**
    * @var array<string, array<string, array{controller: string, method: string, middleware: array<int, string>}>>
    */
    private array $routes = [];

    /**
     * @param array{prefix?: string, middleware?: array<int, string>} $modifiers
     */
    public function group(array $modifiers, callable $callback): void
    {
        $this->currentPrefix = $modifiers['prefix'] ?? '';
        $this->currentMiddlewares = $modifiers['middleware'] ?? [];
        $callback($this);
        $this->currentPrefix = '';
        $this->currentMiddlewares = [];
    }

    /**
    * @param array<int, string> $middleware
    */
    private function addRoute(string $HTTPmethod, string $path, string $controller, string $method, array $middleware = []): void
    {
        $fullPath = $this->currentPrefix . $path;
        $fullMiddlewares = array_merge($this->currentMiddlewares, $middleware);
        $this->routes[$HTTPmethod][$fullPath] = [
            'controller' => $controller,
            'method' => $method,
            'middleware' => $fullMiddlewares,
        ];
    }

    /**
    * @param array<int, string> $middleware
    */
    public function get(string $path, string $controller, string $method, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $controller, $method, $middleware);
    }

    /**
    * @param array<int, string> $middleware
    */
    public function post(string $path, string $controller, string $method, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $controller, $method, $middleware);
    }

    /**
    * @param array<int, string> $middleware
    */
    public function patch(string $path, string $controller, string $method, array $middleware = []): void
    {
        $this->addRoute('PATCH', $path, $controller, $method, $middleware);
    }

    /**
    * @param array<int, string> $middleware
    */
    public function delete(string $path, string $controller, string $method, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $controller, $method, $middleware);
    }

    public function dispatch(): ResponseInterface
    {
        $request = $this->container->get(Request::class);
        $session = $this->container->get(Session::class);
        $path = $request->getPath();
        $method = $request->getMethod();
        $path = rtrim($path, '/');

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
                $response = $controller->$controllerMethod();
                return $response;
            } else {
                throw new InternalServerErrorException("Controller or method not found");
            }
        } else {
            $pathExistsForOtherMethod = false;
            foreach ($this->routes as $httpMethod => $paths) {
                if (array_key_exists($path, $paths)) {
                    $pathExistsForOtherMethod = true;
                    break;
                }
            }
            return $pathExistsForOtherMethod
            ? new JsonResponse(['error' => 'Method Not Allowed'], 405)
            : new JsonResponse(['error' => 'Not Found'], 404);
        }
    }
}
