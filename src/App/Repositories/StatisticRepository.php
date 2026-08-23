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

    public function insertBatch(array $chank, int $organizationId): void
    {
        $sql = 'INSERT INTO statics (country, 
        city, is_active, gender, birth_date, salary, 
        has_children, family_status, registration_date, organization_id)
        VALUES ';
        if (!$chank) {
            throw new ValidationException('Empty chank');
        }
        $columnsCount = count($chank[0]) + 1;
        $placeholder = '(' . implode(', ', array_fill(0, $columnsCount, '?')) . ')';
        if ($columnsCount > 0) {
            $allPlaceholders = array_fill(0, (int)count($chank), $placeholder);
        }
        $placeRow = implode(',', $allPlaceholders);
        $finalSql = $sql . $placeRow;
        $preparedRows = [];
        foreach ($chank as $oneRow) {
            $oneRow[] = $organizationId;
            $preparedRows[] = $oneRow;
        }
        $flatParams = array_merge(...$preparedRows);
        $this->db->execute($finalSql, $flatParams);
    }
}
