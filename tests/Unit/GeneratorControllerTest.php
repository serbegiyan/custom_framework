<?php

namespace Tests\Unit;

use App\Controllers\GeneratorController;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Core\Request;
use App\Services\Localization;
use Core\JsonResponse;
use App\Services\GeneratorService;
use App\Exceptions\ValidationException;

#[CoversClass(GeneratorController::class)]
class GeneratorControllerTest extends TestCase
{
    /**
     * @var \Core\Request&\PHPUnit\Framework\MockObject\MockObject
     */
    private Request $request;

    /**
     * @var \App\Services\Localization&\PHPUnit\Framework\MockObject\MockObject
     */
    private Localization $local;

    /**
     * @var \App\Services\GeneratorService&\PHPUnit\Framework\MockObject\MockObject
     */
    private GeneratorService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createMock(Request::class);
        $this->service = $this->createMock(GeneratorService::class);
        $this->local = $this->createMock(Localization::class);
    }

    public function testIfGenerateCorrect(): void
    {
        $this->local->method('translate')->willReturn('success');
        $this->request->expects($this->once())
            ->method('getInt')->with('quantity', 0)->willReturn(50);

        $generator = new GeneratorController($this->request, $this->service, $this->local);
        $this->service->expects($this->once())->method('run')
            ->with(50, $generator->file);

        $result = $generator->generate();
        $code = $result->getStatusCode();

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertSame(200, $code);
    }

    public function testIfQuantitySubZero(): void
    {
        $this->local->method('translate')->willReturn('success');
        $this->request->expects($this->once())
            ->method('getInt')->with('quantity', 0)->willReturn(-50);

        $generator = new GeneratorController($this->request, $this->service, $this->local);
        
        $this->expectException(ValidationException::class);
        $generator->generate();       
    }
}