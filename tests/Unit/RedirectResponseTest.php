<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Core\RedirectResponse;

#[CoversClass(RedirectResponse::class)]
class RedirectResponseTest extends TestCase
{
    public function testIfGetTargetUrlAndGetBodyCorrect(): void
    {
        $response = new RedirectResponse('testYrl');

        $targetUrl = $response->getTargetUrl();
        $body = $response->getBody();

        $this->assertSame('testYrl', $targetUrl);
        $this->assertSame('', $body);
    }
    //rest methods covered in JsonResponseTest
}