<?php

namespace App\DTO;

use App\Enums\FamilyStatus;
use App\Enums\Gender;
use DateTime;

final readonly class CsvRow
{
    public function __construct(
        public string $country,
        public string $city,
        public bool $isActive,
        public Gender $gender,
        public DateTime $birthDate,
        public int $salary,
        public bool $hasChildren,
        public FamilyStatus $familyStatus,
        public DateTime $registrationDate,
    ) {
    }

    /**
     * @return array<int, int|string|bool>
     */
    public function toDatabaseArray(): array
    {
        return [
            $this->country,
            $this->city,
            $this->isActive ? 1 : 0,
            $this->gender->value,
            $this->birthDate->format('Y-m-d'),
            $this->salary,
            $this->hasChildren ? 1 : 0,
            $this->familyStatus->value,
            $this->registrationDate->format('Y-m-d'),
        ];
    }
}
