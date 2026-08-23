<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Services\GeneratorService;
use Faker\Factory;
use RuntimeException;

#[CoversClass(GeneratorService::class)]
class GeneratorServiceTest extends TestCase
{
    private Factory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new Factory();
    }

    public function testIfFileGenerateCorrect(): void
    {
        $path = __DIR__ . '/../storage/generator.csv';
        $service = new GeneratorService($this->factory);

        $service->run(5, $path);

        $this->assertFileExists($path);

        $size = filesize($path);
        $this->assertGreaterThan(0, $size);

        $rows = file($path);
        $this->assertIsArray($rows);
        $this->assertCount(6, $rows);

        $firstRow = $rows[0];
        $firstWord = str_getcsv($firstRow);
        $this->assertSame('country', $firstWord[0]);
    }

    public function testIfWrongPath(): void
    {
        $path = __DIR__ . '/../wrongPath/generator.csv';
        $service = new GeneratorService($this->factory);
        $this->expectException(RuntimeException::class);
        $service->run(5, $path);
    }
}