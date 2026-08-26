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
use PHPUnit\Framework\MockObject\Stub; 
use Core\JsonResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;

#[CoversClass(Router::class)]
class RouterTest extends TestCase
{
    private Container $container;

    /** @var Stub&Session */
    private $sessionMock;

    /** @var Stub&Request */
    private $requestMock;

    /** @var Stub&DatabaseInterface */
    private $dbMock;

    /** @var Stub&Localization */
    private $localMock;

    /** @var MockObject&OrganizationController */
    private $controllerMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
        $this->requestMock = $this->createStub(Request::class);
        $this->sessionMock = $this->createStub(Session::class);
        $this->dbMock = $this->createStub(DatabaseInterface::class);
        $this->localMock = $this->createStub(Localization::class);
        $this->controllerMock = $this->createMock(OrganizationController::class);

        $this->container->set(Request::class, fn () => $this->requestMock);
        $this->container->set(Session::class, fn () => $this->sessionMock);
        $this->container->set(ContainerInterface::class, fn () => $this->container);
        $this->container->set(DatabaseInterface::class, fn () => $this->dbMock);
        $this->container->set(Localization::class, fn () => $this->localMock); 
        $this->container->set(OrganizationController::class, fn () => $this->controllerMock); 
    }   

    private function mockRequest(string $method, string $path, int $id): void
    {
        $this->requestMock->method('getMethod')->willReturn($method);
        $this->requestMock->method('getPath')->willReturn($path);
        $this->requestMock->method('getUserId')->willReturn($id);        
    }
    
    #[DataProvider('filterProvider')]
    public function testIfGetRequestProsessCorrectly(string $HttpMethod, string $method, string $path, int $status): void
    {    
        $this->mockRequest($HttpMethod, '/organizations', 1);   
        $this->controllerMock->expects($this->once())
            ->method($method)->willReturn(new JsonResponse(['status' => 'ok'], $status));

        $router = new Router($this->container);
        $router->$path('/organizations', OrganizationController::class, $method);
        $result = $router->dispatch();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($status, $result->getStatusCode());
    }    

    /**
     * @return array<string, array<string, string|int>>
     */
    public static function filterProvider(): array
    {
        return [
            'get' => [
                'HttpMethod' => 'GET',
                'method' => 'index',
                'path' => 'get',
                'status' => 200
            ],
            'post' => [
                'HttpMethod' => 'POST',
                'method' => 'store',
                'path' => 'post',
                'status' => 201
            ],
            'patch' => [
                'HttpMethod' => 'PATCH',
                'method' => 'update',
                'path' => 'patch',
                'status' => 200
            ],
            'delete' => [
                'HttpMethod' => 'DELETE',
                'method' => 'delete',
                'path' => 'delete',
                'status' => 204
            ],
        ];
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