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

    /** @var MockObject&Session */
    private $sessionMock;

    /** @var Stub&Request */
    private $requestMock;

    /** @var Stub&Request */
    private $dbMock;

    /** @var Stub&Request */
    private $localMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
        $this->requestMock = $this->createStub(Request::class);
        $this->sessionMock = $this->createStub(Session::class);
        $this->dbMock = $this->createStub(DatabaseInterface::class);
        $this->localMock = $this->createStub(Localization::class);

        $this->container->set(Request::class, fn () => $this->requestMock);
        $this->container->set(Session::class, fn () => $this->sessionMock);
        $this->container->set(ContainerInterface::class, fn () => $this->container);
        $this->container->set(DatabaseInterface::class, fn () => $this->dbMock);
        $this->container->set(Localization::class, fn () => $this->localMock); 
    }   

    private function mockRequest(string $method, string $path, int $id): void
    {
        $this->requestMock->method('getMethod')->willReturn($method);
        $this->requestMock->method('getPath')->willReturn($path);
        $this->requestMock->method('getUserId')->willReturn($id);        
    }
    
    public function testIfGetRequestProsessCorrectly(): void
    {    
        $this->mockRequest('GET', '/organizations', 1);
        
        $message = $this->localMock->method('translate')->willreturn('success');                
        
        $router = new Router($this->container);
        $router->get('/organizations', OrganizationController::class, 'index');
        $result = $router->dispatch();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    public function testItReturns404IfRouteDoesNotExist(): void
    {
        $this->mockRequest('GET', '/organiz', 1);

        $router = new Router($this->container);
        $router->get('/organizations', OrganizationController::class, 'index');
        $result = $router->dispatch();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(404, $result->getStatusCode());
    }

    public function testItReturns405IfMethodIsNotAllowed(): void
    {
        $this->mockRequest('POST', '/organizations', 1);

        $router = new Router($this->container);
        $router->get('/organizations', OrganizationController::class, 'index');
        $result = $router->dispatch();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(405, $result->getStatusCode());
    }
}