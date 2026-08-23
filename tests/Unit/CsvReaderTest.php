<?php

namespace Tests\Unit;

use App\Services\CsvReader;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CsvReader::class)]
class CsvReaderTest extends TestCase
{
    public function testIfFileCorrect(): void
    {
        $path = __DIR__ . '/../storage/test.csv';
        $csvReader = new CsvReader();
        $result = [];
        foreach($csvReader->readRows($path) as $key => $value){
            $result[$key] = $value;
        }
        
        $this->assertArrayHasKey('headers', $result);
        $this->assertNotEmpty($result['headers']);
    }

    public function testIfFileIncorrect(): void
    {
        $path = __DIR__ . '/wrongPath/test.csv';
        $csvReader = new CsvReader();
        $result = [];
        $this->expectException(\RuntimeException::class);

        foreach($csvReader->readRows($path) as $key => $value){
            $result[$key] = $value;
        }        
    }
}