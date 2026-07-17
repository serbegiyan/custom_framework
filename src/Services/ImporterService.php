<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Request;

class ImporterService
{
    public function import($pdo, $file)
    {
        $fp = null;        

        if($file === null){
            echo 'File not found';
            return;
        }        
        $sql = 'INSERT INTO users (country, 
        city, is_active, gender, birth_date, salary, 
        has_children, family_status, registration_date)
        values (:country, :city, :is_active, :gender, :birth_date,
        :salary, :has_children, :family_status, :registration_date)'; 

        $stm = $pdo->prepare($sql);

        $pdo->beginTransaction();
        try{
            $fp = fopen($file, "r");
            $fake = fgetcsv($fp);

            while(($row = fgetcsv($fp)) !== false){                
                $salary = (int)$row[5];                

                $stm->execute([
                    ':country' => trim($row[0]), 
                    ':city' => trim($row[1]),
                    ':is_active' => trim($row[2]),
                    ':gender' => trim($row[3]),
                    ':birth_date' => trim($row[4]),
                    ':salary' => $salary, 
                    ':has_children' => trim($row[6]),
                    ':family_status' => trim($row[7]),
                    ':registration_date' => trim($row[8]),
                ]);            
            }
            $pdo->commit();
            echo "Import complete";

        }catch(\Exception $e){
            $pdo->rollBack();
            echo $e;
        }finally{
            if($fp){
                fclose($fp); 
            }
        }    
        
    }    
}