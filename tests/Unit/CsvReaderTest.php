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

    public function testReadRowsSkipsEmptyRowsUsingExistingStorageFile(): void
    {
        $reader = new CsvReader();
        $filePath = __DIR__ . '/../storage/empty_lines.csv'; 

        $generator = $reader->readRows($filePath);

        $rows = [];
        foreach ($generator as $key => $value) {
            if ($key === 'row') {
                $rows[] = $value;
            }
        }

        $this->assertCount(2, $rows);
        $this->assertSame('Google', $rows[0][0]);
        $this->assertSame('Netflix', $rows[1][0]);
    }
}
