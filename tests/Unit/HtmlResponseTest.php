<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Core\HtmlResponse;

#[CoversClass(HtmlResponse::class)]
class HtmlResponseTest extends TestCase
{
    public function testIfGetBodyCorrect(): void
    {
        $response = new HtmlResponse('<p>Success</p>');
        $body = $response->getBody();

        $this->assertSame('<p>Success</p>', $body);
    }

    //rest methods covered in JsonResponseTest
}