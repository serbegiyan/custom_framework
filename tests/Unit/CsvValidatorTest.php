<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Validators\CsvValidator;
use App\Exceptions\ValidationException;
use Tests\Helpers\CsvRowFactory;

class CsvValidatorTest extends TestCase
{
    public function testIfRowsValid(): void
    {
        $expected = CsvRowFactory::create();
        $validator = new CsvValidator();
        
        $headers = ['country', 'city', 'is_active', 'gender', 'birth_date', 'salary', 'has_children', 'family_status', 'registration_date'];
        $row     = ['USA',     'NY',   '1',         'male',   '1990-01-01', '5000',   '1',            'single',        '2020-01-01'];
        
        $result = $validator->validate($row, $headers);  
                
        $this->assertEquals($expected, $result);
    }

    public function testIfRowsCountInvalid(): void
    {
        $validator = new CsvValidator();
        $headers = ['country', 'city', 'is_active'];
        $row = ['USA', 'NY'];
        
        $this->expectException(ValidationException::class);
        $validator->validate($row, $headers);    
    }    

    #[DataProvider('filterProvider')]
    public function testIfTypesOrBusinessRulesInvalid(array $row, array $headers): void
    {
        $validator = new CsvValidator();        
        $this->expectException(ValidationException::class);
        $validator->validate($row, $headers);    
    }

    public static function filterProvider(): array
    {
        $headers = ['is_active', 'gender', 'family_status', 'birth_date', 'salary', 'has_children', 'registration_date'];

        return [
            'is_active' => [
                'row' => ['wrong', 'male', 'single', '1990-01-01', '5000', '0', '2020-01-01'],                
                'headers' => $headers,
            ],
            'gender' => [
                'row' => ['1', 'wrong', 'single', '1990-01-01', '5000', '0', '2020-01-01'], 
                'headers' => $headers,
            ],
            'family_status' => [
                'row' => ['1', 'male', 'wrong', '1990-01-01', '5000', '0', '2020-01-01'], 
                'headers' => $headers,
            ],
            'birth_date' => [
                'row' => ['1', 'male', 'single', 'wrong', '5000', '0', '2020-01-01'], 
                'headers' => $headers,
            ],
            'salary' => [
                'row' => ['1', 'male', 'single', '1990-01-01', 'wrong', '0', '2020-01-01'], 
                'headers' => $headers,
            ],
            'has_children' => [
                'row' => ['1', 'male', 'single', '1990-01-01', '5000', 'wrong', '2020-01-01'], 
                'headers' => $headers,
            ],
            'registration_date' => [
                'row' => ['1', 'male', 'single', '1990-01-01', '5000', '0', 'wrong'], 
                'headers' => $headers,
            ],
            'invalid_salary' => [
                'row' => ['1', 'male', 'single', '1990-01-01', '-5000', '0', '2020-01-01'], 
                'headers' => $headers,
            ],
            'invalid_birth_date' => [
                'row' => ['1', 'male', 'single', '2050-01-01', '5000', '0', '2020-01-01'], 
                'headers' => $headers,
            ],
            'invalid_registration_date' => [
                'row' => ['1', 'male', 'single', '2005-01-01', '5000', '0', '2000-01-01'], 
                'headers' => $headers,
            ],
        ];
    }
}
