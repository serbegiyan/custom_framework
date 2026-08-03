<?php

namespace App\Services;

use App\DTO\CsvRow;
use App\Exceptions\ValidationException;
use App\Interfaces\DatabaseInterface;
use App\Validators\CsvValidator;
use App\ValueObjects\OrganizationId;
use Generator;

class ImporterService
{
    public function __construct(
        public CsvValidator $validator,
        public DatabaseInterface $db
    ) {
    }

    public function import(OrganizationId $organizationId, string|null $file): void
    {
        $fp = null;

        if ($file === null) {
            echo 'File not found';
            return;
        }
        $sql = 'INSERT INTO statics (country, 
        city, is_active, gender, birth_date, salary, 
        has_children, family_status, registration_date, organization_id)
        VALUES ';

        $this->db->beginTransaction();
        try {
            $columnsCount = 0;
            $chank = [];
            $chunkSize = 500;
            $placeholder = '(' . implode(', ', array_fill(0, $columnsCount, '?')) . ')';
            $rowCount = 0;
            $fileLine = 1;
            $openFile = fopen($file, "r");
            $rawHeaders = $openFile ? (fgetcsv($openFile) ?: []) : [];
            if ($openFile) {
                fclose($openFile);
            }
            /** @var array<int, string> $headers */
            $headers = array_map(fn ($item) => (string)$item, $rawHeaders);
            foreach ($this->readRows($file) as $row) {
                $rowCount++;
                $fileLine++;
                try {
                    /** @var CsvRow $dto */
                    $dto = $this->validator->validate($row, $headers);
                    if ($columnsCount === 0) {
                        $columnsCount = count($dto->toDatabaseArray()) + 1;
                        $placeholder = '(' . implode(', ', array_fill(0, $columnsCount, '?')) . ')';
                    }
                    $rowArray = $dto->toDatabaseArray();
                    $rowArray[] = $organizationId->orgId; 
                    $chank = array_merge($chank, $rowArray);
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
                    $this->db->execute($finalSql, $chank);
                    $chank = [];
                    $rowCount = 0;
                }
            }
            if (!empty($chank)) {
                $allPlaceholders = array_fill(0, (int)(count($chank) / $columnsCount), $placeholder);
                $placeRow = implode(',', $allPlaceholders);
                $finalSql = $sql . $placeRow;
                $this->db->execute($finalSql, $chank);
            }
            $this->db->commit();
            echo "Import complete";
        } catch (\Exception $e) {
            $this->db->rollBack();
            echo $e;
        }
    }

    private function readRows(string $file): Generator
    {
        $fp = fopen($file, "r");
        if (! $fp) {
            echo 'File not found';
            return;
        }
        fgetcsv($fp); 
        while (($row = fgetcsv($fp)) !== false) {
            yield $row;
        }

        fclose($fp);
    }
}
