<?php

namespace App\Validators;

use App\DTO\CsvRow;
use App\Exceptions\ValidationException;
use DateTime;

class CsvValidator
{
    /**
     * @param array<int, string> $row
     * @param array<int, string> $headers
    */
    public function validate(array $row, array $headers): CsvRow
    {

        $headers = array_values($headers);
        $row = array_values($row);
        $this->validateColumnCount($row, $headers);

        $prepered = array_combine($headers, $row);

        $this->validateNotEmpty($prepered);

        $this->validateTypes($prepered);

        /** @var DateTime $bir_date */
        $bir_date = DateTime::createFromFormat('Y-m-d', trim($prepered['birth_date']));

        /** @var DateTime $reg_date */
        $reg_date = DateTime::createFromFormat('Y-m-d', trim($prepered['registration_date']));

        $this->validateBusinessRules($prepered, $bir_date, $reg_date);

        return new CsvRow($prepered, $bir_date, $reg_date);
    }

    /**
     * @param array<int, string> $row
     * @param array<int, string> $headers
     */
    private function validateColumnCount(array $row, array $headers): void
    {
        if (count($row) !== count($headers)) {
            throw new ValidationException("Row has invalid count");
        }
    }

    /**
     * @param array<string, string> $prepered
     */
    private function validateNotEmpty(array $prepered): void
    {
        if (count(array_filter($prepered, fn ($v) => trim($v) !== '')) === 0) {
            throw new ValidationException("Row is empty");
        }
    }

    /**
     * @param array<string, string> $prepered
     */
    private function validateTypes(array $prepered): void
    {
        //is_active
        if (! in_array(trim($prepered['is_active']), ['1', '0', 'true', 'false'])) {
            throw new ValidationException("Column 'is_active' has invalid type");
        }
        //gender
        if (! in_array(trim($prepered['gender']), ['male', 'female'])) {
            throw new ValidationException("Column 'gender' has invalid type");
        }
        //birth_date
        if (! DateTime::createFromFormat('Y-m-d', trim($prepered['birth_date']))) {
            throw new ValidationException("Column 'birth_date' has invalid type");
        }
        //salary
        if (! is_numeric($prepered['salary'])) {
            throw new ValidationException("Column 'salary' has invalid type");
        }
        //has_children
        if (! in_array(trim($prepered['has_children']), ['1', '0', 'true', 'false'])) {
            throw new ValidationException("Column 'has_children' has invalid type");
        }
        //registration_date
        if (! DateTime::createFromFormat('Y-m-d', trim($prepered['registration_date']))) {
            throw new ValidationException("Column 'registration_date' has invalid type");
        }
    }

    /**
     * @param array<string, string> $prepered
     */
    private function validateBusinessRules(array $prepered, DateTime $bir_date, DateTime $reg_date): void
    {
        //salary
        if ((int)(trim($prepered['salary'])) < 0) {
            throw new ValidationException("Column 'salary' has invalid value");
        }
        //birth_date
        $now = new DateTime();
        if ($bir_date > $now) {
            throw new ValidationException("Column 'birth_date' has invalid value");
        }
        //registration_date
        if ($reg_date < $bir_date) {
            throw new ValidationException("Column 'registration_date' has invalid type");
        }
    }
}
