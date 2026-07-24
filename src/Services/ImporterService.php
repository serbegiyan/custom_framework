<?php

namespace App\Services;

use App\DTO\CsvRow;
use App\Exceptions\ValidationException;
use App\Interfaces\DatabaseInterface;
use App\Validators\CsvValidator;
use Generator;

class ImporterService
{
    public function __construct(
        public CsvValidator $validator
    ) {
    }

    public function import(DatabaseInterface $db, string|null $file): void
    {
        $fp = null;

        if ($file === null) {
            echo 'File not found';
            return;
        }
        $sql = 'INSERT INTO users (country, 
        city, is_active, gender, birth_date, salary, 
        has_children, family_status, registration_date, organization_id)
        VALUES ';

        $db->beginTransaction();
        try {
            $columnsCount = 0;
            $chank = [];
            $chunkSize = 500;
            $placeholder = '(' . implode(', ', array_fill(0, $columnsCount, '?')) . ')';
            $rowCount = 0;
            $fileLine = 1;
            $headers = [];

            foreach ($this->readRows($file, $headers) as $row) {
                $rowCount++;
                $fileLine++;
                try {
                    /** @var CsvRow $dto */
                    $dto = $this->validator->validate($row, $headers);
                    if ($columnsCount === 0) {
                        $columnsCount = count($dto->toDatabaseArray());
                        $placeholder = '(' . implode(', ', array_fill(0, $columnsCount, '?')) . ')';
                    }
                    $chank = array_merge($chank, $dto->toDatabaseArray());
                } catch (ValidationException $message) {
                    echo "Error in line {$fileLine}:"  . $message->getMessage() . PHP_EOL;
                    $rowCount--;
                }

                if ($rowCount < $chunkSize) {
                    continue;
                } else {
                    $allPlaceholders = array_fill(0, (int)(count($chank) / $columnsCount), $placeholder);
                    $placeRow = implode(',', $allPlaceholders);
                    $finalSql = $sql . $placeRow;
                    $db->execute($finalSql, $chank);
                    $chank = [];
                    $rowCount = 0;
                }
            }
            if (!empty($chank)) {
                $allPlaceholders = array_fill(0, (int)(count($chank) / $columnsCount), $placeholder);
                $placeRow = implode(',', $allPlaceholders);
                $finalSql = $sql . $placeRow;
                $db->execute($finalSql, $chank);
            }
            $db->commit();
            echo "Import complete";
        } catch (\Exception $e) {
            $db->rollBack();
            echo $e;
        }
    }

    /**
     * @param array<int, string> $headers
    */
    private function readRows(string $file, array $headers): Generator
    {
        $fp = fopen($file, "r");
        if (! $fp) {
            echo 'File not found';
            return;
        }
        $headers = fgetcsv($fp) ?: [];

        while (($row = fgetcsv($fp)) !== false) {
            yield $row;
        }

        fclose($fp);
    }
}
