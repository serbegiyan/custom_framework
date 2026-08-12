<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Core\Session;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

#[CoversClass(Session::class)]
class SessionTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testItCanSetAndGetValues()
    {
        $_SESSION = [];
        $session = new Session();
        $session->set('testKey', 'testValue');
        $value = $session->get('testKey');

        $this->assertSame('testValue', $value);
    }

    #[RunInSeparateProcess]
    public function testWhatReturnGetWithoutKey()
    {
        $_SESSION = [];
        $session = new Session();
        $value = $session->get('secondKey');

        $this->assertSame(null, $value);
    }

    #[RunInSeparateProcess]
    public function testIfOperationProcessCorrect()
    {
        $_SESSION = [];
        $session = new Session();
        $session->set('thirdKey', 'thirdValue');

        $has = $session->has('thirdKey');
        $this->assertSame(true, $has);

        $remove = $session->remove('thirdKey');
        $this->assertSame(false, $session->has('thirdKey'));

        $session->set('fourthKey', 'fourthValue');
        $session->destroy();
        $this->assertSame([], $_SESSION);
    }
}