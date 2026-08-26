<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Container;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Stubs\SampleService;
use Tests\Stubs\TestInterface;
use Tests\Stubs\DependService;
use Tests\Stubs\ClassWithBuiltInParam;
use App\Services\ServiceNotFoundException;

#[CoversClass(Container::class)]
class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function testIfObjectSet(): void
    {
        $this->container->set('testId', function(){
            return 'success';
        });

        $result = $this->container->get('testId');

        $this->assertSame('success', $result);
    }

    public function testIfObjectHasRuncorrect(): void
    {
        $this->container->set('testId', function(){
            return 'success';
        });

        $result = $this->container->has('testId');
        $wrong = $this->container->has('WrongId');

        $this->assertSame(true, $result);
        $this->assertSame(false, $wrong);
    }

    public function testItReturnsTheSameInstance(): void
    {
        $this->container->set('testId', function(){
            return new \stdClass();
        });

        $first = $this->container->get('testId');
        $second = $this->container->get('testId');

        $this->assertSame($first, $second);
    }

    public function testItCanCreateClassWithoutConstructor(): void
    {
        $result = $this->container->get(SampleService::class);

        $this->assertInstanceOf(SampleService::class, $result);
    }

    public function testItResolvesDependenciesAutomatically(): void
    {
        $result = $this->container->get(DependService::class);
        $this->assertInstanceOf(DependService::class, $result);

        $depend = $result->service;
        $this->assertInstanceOf(SampleService::class, $depend);
    }

    public function testItThrowsExceptionForBuiltInParametersWithoutDefault(): void
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->container->get(ClassWithBuiltInParam::class);
    }

    public function testItThrowsExceptionIfClassDoesNotExist(): void
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->container->get('SomeNonExistentClass');
    }

    public function testItThrowsExceptionIfClassNotInstantiable(): void
    {
        $this->expectException(ServiceNotFoundException::class);

        $this->container->get(TestInterface::class);
    }
}