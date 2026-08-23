<?php

namespace Tests\Unit;

use App\Controllers\AuthController;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Core\Request;
use Core\Session;
use App\Services\Localization;
use Core\RedirectResponse;
use Core\JsonResponse;
use App\Services\AuthService;
use App\Exceptions\ValidationException;

#[CoversClass(AuthController::class)]
class AuthControllerTest extends TestCase
{
    /**
     * @var \Core\Session&\PHPUnit\Framework\MockObject\MockObject
     */
    private Session $session;

    /**
     * @var \Core\Request&\PHPUnit\Framework\MockObject\MockObject
     */
    private Request $request;

    /**
     * @var \App\Services\Localization&\PHPUnit\Framework\MockObject\MockObject
     */
    private Localization $local;

    /**
     * @var \App\Services\AuthService&\PHPUnit\Framework\MockObject\MockObject
     */
    private AuthService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createMock(Request::class);
        $this->session = $this->createMock(Session::class);
        $this->service = $this->createMock(AuthService::class);
        $this->local = $this->createMock(Localization::class);
    }

    public function testIfLoginAndLogoutCorrect(): void
    {
        $this->request
            ->method('getString')->willReturnMap([
            ['email', 'test@test.com'],
            ['password', '12345678']
        ]);
        
        $this->service->method('loginUser')->willReturn(42);
        $this->local->method('translate')->willReturn('success');
        $this->session->expects($this->once())
            ->method('set')
            ->with('user_id', 42);
        $controller = new AuthController($this->request, $this->session,
        $this->local, $this->service);
        $response = $controller->login();
        $code = $response->getStatusCode();
        $this->assertSame(200, $code);

        $this->session->expects($this->once())
            ->method('destroy');
        $result = $controller->logout();
        $cookies = $result->getCookies();
        $sessionName = (string)session_name();

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertArrayHasKey($sessionName, $cookies);
        $this->assertSame('', $cookies['PHPSESSID']['value']);
    }

    public function testIfRegistrationCorrect(): void
    {
        $this->request
            ->method('getString')->willReturnMap([
                ['name', 'TestName'],
                ['email', 'test@test.com'],
                ['password', '12345678']
            ]);
        
        $this->service->method('registerUser')->willReturn(42);
        $this->local->method('translate')->willReturn('success');
        $this->session->expects($this->once())
            ->method('set')
            ->with('user_id', 42);
        $controller = new AuthController($this->request, $this->session,
        $this->local, $this->service);
        $response = $controller->register();
        $code = $response->getStatusCode();
        $this->assertSame(201, $code);
    }

    public function testIfLoginInvalid(): void
    {
        $this->request
            ->method('getString')->willReturnMap([
            ['email', 'test@test.com'],
            ['password', '12345678']
        ]);
        $this->service->method('loginUser')
            ->willThrowException(new ValidationException('Invalid inputs values'));
        $controller = new AuthController($this->request, $this->session,
            $this->local, $this->service);

        $this->expectException(ValidationException::class);       
        
        $controller->login();
    }

    public function testExceptionIfUserExists(): void
    {
        $this->request
            ->method('getString')->willReturnMap([
            ['name', 'TestName'],
            ['email', 'test@test.com'],
            ['password', '12345678']
        ]);
        $this->service->method('registerUser')
            ->willThrowException(new ValidationException('user_already_exists'));
        $controller = new AuthController($this->request, $this->session,
            $this->local, $this->service);

        $this->expectException(ValidationException::class);       
        
        $controller->register();
    }
}