<?php

use App\Controllers\AnalizeController;
use App\Controllers\AuthController;
use App\Controllers\GeneratorController;
use App\Controllers\ImporterController;
use App\Controllers\LanguageController;
use App\Controllers\OrganizationController;
use App\Interfaces\ContainerInterface;
use App\Interfaces\MiddlewareInterface;
use App\Middlewares\AuthMiddleware;

$router->group(['middleware' => [AuthMiddleware::class]], function($router){
    $router->get('/statics', AnalizeController::class, 'index', []);
    $router->get('/organizations', OrganizationController::class, 'index', []);

    $router->post('/organizations', OrganizationController::class, 'store', []);
    $router->patch('/organizations', OrganizationController::class, 'update', []);
    $router->delete('/organizations', OrganizationController::class, 'delete', []);

    $router->post('/statics/imports', ImporterController::class, 'store', []);
    $router->post('/statics/generations', GeneratorController::class, 'generate', []);
});

$router->group(['prefix' => '/users'], function($router){
    $router->post('/login', AuthController::class, 'login', []);
    $router->post('/registration', AuthController::class, 'register', []);
    $router->post('/logout', AuthController::class, 'logout', []);
});

$router->post('/lang/switch', LanguageController::class, 'switchLang', []);
