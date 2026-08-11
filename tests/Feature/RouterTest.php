<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Controllers\OrganizationController;
use App\Interfaces\ContainerInterface;
use App\Interfaces\DatabaseInterface;
use Core\Container;
use Core\Request;
use Core\Session;
use Core\Router;
use App\Services\Localization;
use App\Interfaces\ResponseInterface;

#[CoversClass(RouterTest::class)]
class RouterTest extends TestCase
{
    private Container $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }   
    
    public function testIfGetRequestProsessCorrectly(): void
    {
        $request = $this->createStub(Request::class);
        $method = $request->method('getMethod')
            ->willReturn('GET');
        $path = $request->method('getPath')
            ->willReturn('/organizations');
        $requestId = $request->method('getUserId')
            ->willReturn(1);

        $session = $this->createMock(Session::class);
        $sessionId = $session->expects($this->any())
            ->method('get')
            ->with('user_id')
            ->willreturn(1);

        $db = $this->createStub(DatabaseInterface::class); 
        
        $local = $this->createStub(Localization::class);
        $message = $local->method('translate')
            ->willreturn('success');

        $this->container->set(Request::class, function () use ($request){
            return $request;
        });
        $this->container->set(Session::class, function () use ($session){
            return $session;
        });
        $this->container->set(DatabaseInterface::class, function () use ($db){
            return $db;
        });
        $this->container->set(Localization::class, function () use ($local){
            return $local;
        });
        $this->container->set(ContainerInterface::class, function (){
            return $this->container;
        });
        
        $router = new Router($this->container);
        $router->get('/organizations', OrganizationController::class, 'index');
        $result = $router->dispatch();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }
}