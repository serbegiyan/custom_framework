<?php

namespace App\Services;

use App\Interfaces\DatabaseInterface;

class ImporterService
{
    public function import(DatabaseInterface $db, string|null $file): void
    {
        $fp = null;

        if ($file === null) {
            echo 'File not found';
            return;
        }
        $sql = 'INSERT INTO users (country, 
        city, is_active, gender, birth_date, salary, 
        has_children, family_status, registration_date)
        VALUES ';

        $db->beginTransaction();
        try {
            $fp = fopen($file, "r");
            if (is_resource($fp)) {
                $headers = fgetcsv($fp);
                if ($headers === false) {
                    echo 'Empty file';
                    return;
                }
                $columnsCount = count($headers);
                $chank = [];
                $chunkSize = 1000;
                $placeholder = '(' . implode(', ', array_fill(0, $columnsCount, '?')) . ')';

                while (!empty($row = fgetcsv($fp))) {

                    $chank[] = trim($row[0] ?? '');
                    $chank[] = trim($row[1] ?? '');
                    $chank[] = trim($row[2] ?? '');
                    $chank[] = trim($row[3] ?? '');
                    $chank[] = trim($row[4] ?? '');
                    $chank[] = (int)($row[5] ?? 0);
                    $chank[] = trim($row[6] ?? '');
                    $chank[] = trim($row[7] ?? '');
                    $chank[] = trim($row[8] ?? '');

                    $amount = $chunkSize * $columnsCount;

                    if (count($chank) < $amount) {
                        continue;
                    } else {
                        $allPlaceholders = array_fill(0, (int)(count($chank) / $columnsCount), $placeholder);
                        $placeRow = implode(',', $allPlaceholders);
                        $finalSql = $sql . $placeRow;
                        $db->execute($finalSql, $chank);
                        $chank = [];
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
            }
        } catch (\Exception $e) {
            $db->rollBack();
            echo $e;
        } finally {
            if ($fp) {
                fclose($fp);
            }
        }
    }
}
