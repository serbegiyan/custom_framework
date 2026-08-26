<?php
namespace Tests\Unit;

use App\DTO\CsvRow;
use App\Enums\Gender;
use App\Enums\FamilyStatus;
use DateTime;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\CsvRowFactory;

final class CsvRowTest extends TestCase
{
    public function testIfCorrectlyInstantiatesAndConvertsTypes(): void
    {
        $row = CsvRowFactory::create([
            'country'          => 'France',
            'city'             => 'Paris',
            'isActive'         => true, 
            'gender'           => Gender::Female, 
            'birthDate'        => new DateTime('1990-01-01'),
            'salary'           => 3500,    
            'hasChildren'      => true,  
            'familyStatus'     => FamilyStatus::Single, 
            'registrationDate' => new DateTime('2020-01-01'),
        ]);

        $this->assertSame('France', $row->country);
        $this->assertSame('Paris', $row->city);
        $this->assertTrue($row->isActive);
        $this->assertSame(Gender::Female, $row->gender); // Теперь это Enum
        $this->assertSame('1990-01-01', $row->birthDate->format('Y-m-d'));
        $this->assertSame(3500, $row->salary);
        $this->assertTrue($row->hasChildren);
        $this->assertSame(FamilyStatus::Single, $row->familyStatus); // Теперь это Enum
        $this->assertSame('2020-01-01', $row->registrationDate->format('Y-m-d'));
    }

    public function testIfHandlesFalseBooleanValues(): void
    {

        $row = CsvRowFactory::create([
            'isActive'    => false,
            'hasChildren' => false,
        ]);

        $this->assertFalse($row->isActive);
        $this->assertFalse($row->hasChildren);
    }

    public function testIfToDatabaseArrayFormatsCorrectly(): void
    {        
        $row = CsvRowFactory::create([
            'country'          => 'Germany',
            'city'             => 'Berlin',
            'isActive'         => true,
            'gender'           => Gender::Male,
            'birthDate'        => new DateTime('1990-01-01'),
            'salary'           => 6000,
            'hasChildren'      => false,
            'familyStatus'     => FamilyStatus::Divorced,
            'registrationDate' => new DateTime('2020-01-01'),
        ]);

        $expectedDbArray = [
            'Germany',
            'Berlin',
            1,
            'male',
            '1990-01-01',
            6000,
            0,
            'divorced',
            '2020-01-01',
        ];

        $this->assertSame($expectedDbArray, $row->toDatabaseArray());
    }
}
