<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Core\Request;
use RuntimeException;

#[CoversClass(Request::class)]
class RequestTest extends TestCase
{
    protected function tearDown(): void
    {
        $_SERVER = [];
        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_COOKIE = []; 
        
        parent::tearDown();
    }

    public function testIfReturnRightOrDefaultPath(): void
    {
        $_SERVER['REQUEST_URI'] = '/users/profile/';
        $request = new Request();
        $result = $request->getPath();
        $this->assertSame('/users/profile', $result);

        $_SERVER['REQUEST_URI'] = '/';
        $result = $request->getPath();
        $this->assertSame('/', $result);
    }

    public function testIfReturnRightMethodAndParams(): void
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
        $request2 = new Request();
        $method2 = $request2->getMethod();
        $this->assertSame('POST', $method2);
        $params2 = $request->getParams();
        $this->assertSame(['name' => 'Sergey'], $params2);
    }

    public function testIfReturnExceptionWhenInvalidInputs(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PATCH';        
        $request = new Request();
        $method = $request->getMethod();
        $this->assertSame('PATCH', $method);
        $this->expectException(RuntimeException::class);
        $request->getParams();
    }

    public function testIfInvalidHttpMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'WrongMethod';        
        $request = new Request();
        $method = $request->getMethod();
        $this->assertSame('WrongMethod', $method);
        $params = $request->getParams();
        $this->assertSame([], $params);
    }

    public function testIfGetStringRetirnEmptyRowFromArray(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = ['name' => ['Sergey', 'Ivan']];
        $request = new Request();
        $str = $request->getString('name');
        $this->assertSame('', $str);
    }

    public function testIfGetIntReturnZeroFromLetters(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = ['val' => 'ABC'];
        $request = new Request();
        $int = $request->getInt('val');
        $this->assertSame(0, $int);
    }

    public function testIfGetIntReturnDefault(): void
    {    
        $request = new Request();    
        $int = $request->getInt('val');
        $this->assertSame(0, $int);
    }

    public function testIfFileSizeIsCorrect(): void
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

    public function testIfFileSizeIsIncorrect(): void
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

    public function testIfFileMissed(): void
    {
        $request = new Request();
        $size = $request->isValidSize('avatar');
        $this->assertFalse($size);
    }

    public function testIfSetAndGetUserIdCorrect(): void
    {
        $request = new Request();
        $wrong_id = $request->getUserId();
        $this->assertSame(null, $wrong_id);
        
        $request->setUserId(12);
        $user_id = $request->getUserId();        

        $this->assertSame(12, $user_id);
    }

    public function testIfGetCookieCorrect(): void
    {
        $request = new Request();
        $empty = $request->getCookies('test');
        $this->assertSame(null, $empty);

        $_COOKIE['test'] = 'testValue';
        $cookie = $request->getCookies('test');
        $this->assertSame('testValue', $cookie);
    }

    public function testIfGetRefererCorrect(): void
    {
        $request = new Request();
        $empty = $request->getReferer();
        $this->assertSame('/', $empty);

        $_SERVER['HTTP_REFERER'] = 'testReferer';
        $referer = $request->getReferer();
        $this->assertSame('testReferer', $referer);
    }

    public function testIfGetFilesCorrect(): void
    {
        $_FILES['avatar'] = [
            'name'     => 'photo.jpg',
            'type'     => 'image/jpeg',
            'tmp_name' => '/tmp/phpYzdqkD',
            'error'    => UPLOAD_ERR_OK, 
            'size'     => 12345,
        ];
        $request = new Request();
        $avatar = $request->getFiles('avatar');
        $this->assertSame('/tmp/phpYzdqkD', $avatar);

        $empty = $request->getFiles('wrong');
        $this->assertSame(null, $empty);
    }

    public function testGetFilesReturnsNullOnUploadError(): void
    {
        $_FILES['document'] = [
            'name'     => 'doc.pdf',
            'type'     => 'application/pdf',
            'tmp_name' => '',
            'error'    => UPLOAD_ERR_INI_SIZE,
            'size'     => 0,
        ];

        $request = new Request();

        $this->assertNull($request->getFiles('document'));
    }
}