<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Middlewares\AuthMiddleware;
use App\Exceptions\UnauthorizedException;
use App\Interfaces\ResponseInterface;
use Core\JsonResponse;
use Core\Session;
use Core\Request;

#[CoversClass(AuthMiddleware::class)]
class AuthTest extends TestCase
{
    public function testIfHandleRequestCorrect(): void
    {
        $session = $this->createMock(Session::class);
        $request = $this->createMock(Request::class);
        $user_id = 1;

        $session->expects($this->once())
            ->method('get')
            ->with('user_id')
            ->willReturn($user_id);

        $request->expects($this->once())
            ->method('setUserId')
            ->with($user_id);

        $middleware = new AuthMiddleware();
        $result = $middleware->handle($request, $session);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());               
    }

    public function testIfHandleUnauthorizedUser(): void
    {
        $this->expectException(UnauthorizedException::class);

        $session = $this->createMock(Session::class);
        $request = $this->createStub(Request::class);
        
        $session->expects($this->once())
            ->method('get')
            ->with('user_id')
            ->willReturn(null);
        
        $middleware = new AuthMiddleware();
        $middleware->handle($request, $session);    
    }
}
