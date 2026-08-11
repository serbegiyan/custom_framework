<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Core\Request;

#[CoversClass(Request::class)]
class RequestTest extends TestCase
{
    protected function tearDown(): void
    {
        $_SERVER = [];
        $_GET = [];
        $_POST = [];
    }

    public function testIfReturnRightPath()
    {
        $url = '/users/profile';
        $_SERVER['REQUEST_URI'] = '/users/profile/';
        $request = new Request();
        $result = $request->getPath();
        $this->assertSame($url, $result);
        $_SERVER['REQUEST_URI'] = '/';
        $url = '/';
        $result = $request->getPath();
        $this->assertSame($url, $result);
    }

    public function testIfReturnRightMethodAndParams()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = ['name' => 'Sergey'];
        $request = new Request();
        $method = $request->getMethod();
        $this->assertSame('GET', $method);
        $params = $request->getParams();
        $this->assertSame(['name' => 'Sergey'], $params);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['name' => 'Sergey'];
        $request = new Request();
        $method = $request->getMethod();
        $this->assertSame('POST', $method);
        $params = $request->getParams();
        $this->assertSame(['name' => 'Sergey'], $params);
    }

    public function testIfGetStringRetirnEmptyRowFromArray(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = ['name' => ['Sergey', 'Ivan']];
        $request = new Request();
        $str = $request->getString('name');
        $this->assertSame('', $str);
    }

    public function testIfGetIntRetirnValidValue(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = ['val' => 2];
        $request = new Request();
        $int = $request->getInt('val');
        $this->assertIsInt($int);
    }

    public function testIfGetIntRetirnZeroFromLetters(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = ['val' => 'ABC'];
        $request = new Request();
        $int = $request->getInt('val');
        $this->assertSame(0, $int);
    }

    public function testIfFailSizeIsCorrect(): void
    {
        $_FILES['avatar'] = [
            'error' => UPLOAD_ERR_OK,
            'size' => 1000, // 1 КБ
            'tmp_name' => '/tmp/phpXYZ'
        ];
        $request = new Request();
        $size = $request->isValidSize('avatar');
        $this->assertTrue($size);
    }

    public function testIfFailSizeIsIncorrect(): void
    {
        $_FILES['avatar'] = [
            'error' => UPLOAD_ERR_OK,
            'size' => 10000000,
            'tmp_name' => '/tmp/phpXYZ'
        ];
        $request = new Request();
        $size = $request->isValidSize('avatar');
        $this->assertFalse($size);
    }
}