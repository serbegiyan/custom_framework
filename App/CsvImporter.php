<?php

namespace App;
use Core\Database;
use Core\Request;

class CsvImporter
{
    public function __construct(
        public \PDO $pdo, 
        public Request $request,
    )
    {}    

    public function run()
    {
        $fp = null;
        $file = $this->request->getFiles('csv_file');

        if($file === null){
            echo 'File not found';
            return;
        }        
        $sql = 'INSERT INTO users (country, 
        city, is_active, gender, birth_date, salary, 
        has_children, family_status, registration_date)
        values (:country, :city, :is_active, :gender, :birth_date,
        :salary, :has_children, :family_status, :registration_date)'; 

        $stm = $this->pdo->prepare($sql);

        $this->pdo->beginTransaction();
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
            $this->pdo->commit();
            echo "Import complete";

        }catch(\Exception $e){
            $this->pdo->rollBack();
            echo $e;
        }finally{
            if($fp){
                fclose($fp); 
            }
        }    
        
    }    
}