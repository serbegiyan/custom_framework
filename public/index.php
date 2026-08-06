<?php

use Core\Container;
use Core\Database;
use Core\JsonResponse;
use Core\ResponseEmitter;
use Core\Router;
use App\Exceptions\ForbiddenException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidationException;
use App\Interfaces\ContainerInterface;
use App\Interfaces\DatabaseInterface;

require __DIR__ . '/../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/../')->load();

$container = new Container();
$container->set(ContainerInterface::class, function (ContainerInterface $c) {
    return $c;
});
$container->set(DatabaseInterface::class, function (ContainerInterface $c) {
    $dsn = $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
    $user = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];

    return new Database($dsn, $user, $password);
});

$router = $container->get(Router::class);

require __DIR__ . '/../routes/web.php';

$emitter = new ResponseEmitter();
try {
    $response = $router->dispatch();
    $emitter->emit($response);
} catch (ValidationException $e) {
    $responseException = new JsonResponse(['success' => false, 'error' => $e->getMessage()], 422);
    $emitter->emit($responseException);
} catch (ForbiddenException $e) {
    $responseException = new JsonResponse(['success' => false, 'error' => $e->getMessage()], 403);
    $emitter->emit($responseException);
} catch (UnauthorizedException $e) {
    $responseException = new JsonResponse(['success' => false, 'error' => $e->getMessage()], 401);
    $emitter->emit($responseException);
} catch (\Throwable $e) {
    $logMessage = sprintf(
        "[%s] Критическая ошибка: %s в файле %s на строке %d\nСтек вызовов:\n%s\n\n",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    error_log($logMessage, 3, __DIR__ . '/../storage/logs/app.log');
    $responseException = new JsonResponse(['success' => false, 'error' => 'Internal Server Error. Please try again later.'], 500);
    $emitter->emit($responseException);
}
