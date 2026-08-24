<?php

namespace App\Repositories;

use App\Exceptions\ValidationException;
use App\Interfaces\DatabaseInterface;

class StatisticRepository
{
    public function __construct(
        public DatabaseInterface $db,
    ) {
    }

    /**
     * @param array<int, array<int, mixed>> $chank
     */
    public function insertBatch(array $chank, int $organizationId): void
    {
        $sql = 'INSERT INTO statics (country, 
        city, is_active, gender, birth_date, salary, 
        has_children, family_status, registration_date, organization_id)
        VALUES ';
        if (!$chank) {
            throw new ValidationException('Empty chank');
        }
        $columnsCount = count(current($chank)) + 1;
        $placeholder = '(' . implode(', ', array_fill(0, $columnsCount, '?')) . ')';
        $allPlaceholders = array_fill(0, (int)count($chank), $placeholder);

        $placeRow = implode(',', $allPlaceholders);
        $finalSql = $sql . $placeRow;
        $flatParams = [];
        foreach ($chank as $oneRow) {
            $oneRow[] = $organizationId;
            array_push($flatParams, ...$oneRow);
        }
        $this->db->execute($finalSql, $flatParams);
    }
}
