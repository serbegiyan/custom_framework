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
    public function testItCanSetAndGetValues(): void
    {
        $_SESSION = [];
        $session = new Session();
        $session->set('testKey', 'testValue');
        $value = $session->get('testKey');

        $this->assertSame('testValue', $value);
    }

    #[RunInSeparateProcess]
    public function testWhatReturnGetWithoutKey(): void
    {
        $_SESSION = [];
        $session = new Session();
        $value = $session->get('secondKey');

        $this->assertSame(null, $value);
    }

    #[RunInSeparateProcess]
    public function testIfOperationProcessCorrect(): void
    {
        $_SESSION = [];
        $session = new Session();
        $session->set('thirdKey', 'thirdValue');

        $has = $session->has('thirdKey');
        $this->assertSame(true, $has);

        $session->remove('thirdKey');
        $this->assertSame(false, $session->has('thirdKey'));

        $session->set('fourthKey', 'fourthValue');
        $session->destroy();
        $this->assertSame([], $_SESSION);
    }
}