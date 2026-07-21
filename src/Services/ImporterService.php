<?php

namespace App\Services;

use App\Core\Interfaces\DatabaseInterface;

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
        values (:country, :city, :is_active, :gender, :birth_date,
        :salary, :has_children, :family_status, :registration_date)';

        $db->beginTransaction();
        try {
            $fp = fopen($file, "r");
            if ($fp) {
                $fake = fgetcsv($fp);

                while (!empty($row = fgetcsv($fp))) {
                    $salary = (int)$row[5];

                    $db->execute($sql, [
                        ':country' => trim($row[0] ?? ''),
                        ':city' => trim($row[1] ?? ''),
                        ':is_active' => trim($row[2] ?? ''),
                        ':gender' => trim($row[3] ?? ''),
                        ':birth_date' => trim($row[4] ?? ''),
                        ':salary' => $salary,
                        ':has_children' => trim($row[6] ?? ''),
                        ':family_status' => trim($row[7] ?? ''),
                        ':registration_date' => trim($row[8] ?? ''),
                    ]);
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
