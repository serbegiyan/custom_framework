<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Core\ResponseEmitter;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use App\Interfaces\ResponseInterface;

#[CoversClass(ResponseEmitter::class)]
class ResponseEmitterTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testEmitOutputsBody(): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getBody')->willReturn('Hello World');
        $this->expectOutputString('Hello World');
        
        $emitter = new ResponseEmitter();        
        $emitter->emit($response);
    }

    #[RunInSeparateProcess]
    public function testEmitSendsHeadersWhenNotSent(): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(201);
        $response->method('getHeaders')->willReturn(['X-Custom-Header' => 'TestValue']);
        $response->method('getBody')->willReturn('');
                
        $emitter = new ResponseEmitter();
        $emitter->emit($response);

        $this->assertSame(201, http_response_code());
    }
}