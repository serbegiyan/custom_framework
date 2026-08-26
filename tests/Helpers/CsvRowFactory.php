<?php

namespace Tests\Helpers;

use App\DTO\CsvRow;
use App\Enums\Gender;
use App\Enums\FamilyStatus;
use DateTime;

final class CsvRowFactory
{
    /** 
     * @param array<string, mixed> $overrides
     */
    public static function create(array $overrides = []): CsvRow 
    {
        $defaults = [
            'country'          => 'USA',
            'city'             => 'NY',
            'isActive'         => true,
            'gender'           => Gender::Male, 
            'birthDate'        => DateTime::createFromFormat('Y-m-d', '1990-01-01'),
            'salary'           => 5000,
            'hasChildren'      => true,
            'familyStatus'     => FamilyStatus::Single, 
            'registrationDate' => DateTime::createFromFormat('Y-m-d', '2020-01-01'),
        ];

        $data = array_merge($defaults, $overrides);

        return new CsvRow(...$data);
    }
}
