<?php

namespace Core;

class Router
{
    public function __construct(
        public \PDO $pdo,
        public Request $request
    )
    {}

    private array $routes = [
        '/parse' => \App\CsvImporter::class,
        '/analize' => \App\Analizer::class 
    ];

    public function dispatch()
    {
        $path = $this->request->getPath();
        if(array_key_exists($path, $this->routes)){
            $handlerClass = $this->routes[$path];
            $handler = new $handlerClass($this->pdo, $this->request);
            $handler->run();
        }else{
            echo 'Page not found(404)';
            return http_response_code(404);
        }
    }
}