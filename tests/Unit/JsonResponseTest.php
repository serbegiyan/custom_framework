<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Core\JsonResponse;

#[CoversClass(JsonResponse::class)]
class JsonResponseTest extends TestCase
{
    public function testDefaultStatusCodeAndHeaders(): void
    {
        $response = new JsonResponse(['message' => 'success']);

        $status = $response->getStatusCode();
        $this->assertSame(200, $status);

        $headers = $response->getHeaders();
        $this->assertSame(['Content-Type' => 'application/json; charset=utf-8'], $headers);
    }

    public function testCustomStatusCode(): void
    {
        $response = new JsonResponse(['message' => 'success'], 401);

        $status = $response->getStatusCode();
        $this->assertSame(401, $status);
    }

    public function testGetBodyEncodesJsonCorrectly(): void
    {
        $response = new JsonResponse(['message' => 'success', 'id' => 5], 200);

        $body = $response->getBody();
        $this->assertSame('{"message":"success","id":5}', $body);
    }

    public function testThrowsExceptionOnInvalidJson(): void
    {
        $this->expectException(\JsonException::class);
        $invalidUtf8String = "\xEF\xBF\xBD\x50\x44\x46\xB1";
        $response = new JsonResponse(['invalid' => $invalidUtf8String], 200);
        $body = $response->getBody();
    }
}