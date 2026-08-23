<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Services\Gate;
use App\Interfaces\DatabaseInterface;
use App\Services\Localization;
use App\ValueObjects\OrganizationId;
use App\Policies\OrganizationPolicy;
use App\Exceptions\ForbiddenException;
use Core\Container;
use Core\Request;

#[CoversClass(Gate::class)]
class GateTest extends TestCase
{
    private Container $container;

    private Localization $local;

    private Request $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
        $this->request = new Request();
        $this->local = $this->createStub(Localization::class);
        $this->request->setUserId(22);
    }

    public function testIfAuthorizeCorrect(): void
    {
        $this->local->method('translate')->willReturn('success');
        $gate = new Gate($this->container, $this->request, $this->local);
        $policy = $this->createStub(OrganizationPolicy::class);
        $this->container->set(OrganizationPolicy::class, fn() => $policy);
        $policy->method('update')->willReturn(true);
        $orgId = new OrganizationId(3);
        $gate->authorize(OrganizationPolicy::class, 'update', $orgId);
        $this->expectNotToPerformAssertions();    
    }

    public function testIfAuthorizeIncorrect(): void
    {
        $this->local->method('translate')->willReturn('success');
        $gate = new Gate($this->container, $this->request, $this->local);
        $policy = $this->createStub(OrganizationPolicy::class);
        $this->container->set(OrganizationPolicy::class, fn() => $policy);
        $policy->method('update')->willReturn(false);
        $orgId = new OrganizationId(3);
        $this->expectException(ForbiddenException::class);
        $gate->authorize(OrganizationPolicy::class, 'update', $orgId);
    }
}