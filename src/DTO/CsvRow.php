<?php

namespace App\DTO;

use DateTime;
use App\Validators\CsvValidator;

final readonly class CsvRow
{
    public string $country;
    public string $city;
    public bool $is_active;
    public string $gender;
    public DateTime $birth_date;
    public int $salary;
    public bool $has_children;
    public string $family_status;
    public DateTime $registration_date;
    public int $organization_id;

    /**
     * @param array<string, string> $prepered
     */
    public function __construct(
        array $prepered, 
        DateTime $bir_date, 
        DateTime $reg_date
    ){
        $this->country = $prepered['country'];
        $this->city = $prepered['city'];
        $this->is_active = ($prepered['is_active'] === '1' || $prepered['is_active'] === 'true');
        $this->gender = $prepered['gender'];
        $this->birth_date = $bir_date;
        $this->salary = (int) $prepered['salary'];
        $this->has_children = ($prepered['has_children'] === '1' || $prepered['has_children'] === 'true');
        $this->family_status = $prepered['family_status'];
        $this->registration_date = $reg_date;
        $this->organization_id = (int) $prepered['organization_id'];
    } 

    public function toDatabaseArray(): array
    {
        $array = [
            $this->country,
            $this->city,
            $this->is_active ? 1 : 0,
            $this->gender,
            $this->birth_date->format('Y-m-d'),
            $this->salary,
            $this->has_children ? 1 : 0,
            $this->family_status,
            $this->registration_date->format('Y-m-d'),
            $this->organization_id,
        ];

        return $array;
    }
}