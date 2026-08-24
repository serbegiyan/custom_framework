<?php

namespace Tests\Unit;

use App\Controllers\LanguageController;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Services\Localization;
use Core\Request;
use Core\JsonResponse;
use Core\RedirectResponse;
use App\Interfaces\ResponseInterface;

#[CoversClass(LanguageController::class)]
class LanguageControllerTest extends TestCase
{
    /**
     * @var \App\Services\Localization&\PHPUnit\Framework\MockObject\MockObject
     */
    private Localization $local;

    /**
     * @var \Core\Request&\PHPUnit\Framework\MockObject\MockObject
     */
    private Request $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createMock(Request::class);
        $this->local = $this->createMock(Localization::class);
    }

    public function testIfLangSwitchsCorrect(): void
    {            
        $this->request->expects($this->once())
            ->method('getPath')->willReturn('testPath');
        $this->request->expects($this->once())
            ->method('getParams')->willReturn(['lang' => 'ru']);
        $this->request->expects($this->once())
            ->method('getReferer')->willReturn('test/ru');
        $this->request->expects($this->once())
            ->method('getString')->with('lang')->willReturn('ru');
        $this->local->expects($this->once())
            ->method('setLang')
            ->with('ru', $this->isInstanceOf(JsonResponse::class));

        $controller = new LanguageController($this->request, $this->local);
        $result = $controller->switchLang();
        $code = $result->getStatusCode();
        $headers = $result->getHeaders();
        $body = $result->getBody();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $code);
        $this->assertArrayHasKey('Location', $headers);
        $this->assertSame('test/ru', $headers['Location']);
        $this->assertJson($body);
        $this->assertStringContainsString('success', $body);
    }
    
    public function testIfLangSwitchRedirectsWhenNoLangParam(): void
    {
        $this->request->expects($this->once())
            ->method('getPath')->willReturn('testPath');
        $this->request->expects($this->once())
            ->method('getParams')->willReturn([]);

        $this->local->expects($this->never())->method('setLang');

        $controller = new LanguageController($this->request, $this->local);
        $result = $controller->switchLang();
        $code = $result->getStatusCode();
        $headers = $result->getHeaders();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(302, $code);
        $this->assertArrayHasKey('Location', $headers);
        $this->assertSame('/', $headers['Location']);        
    }
}