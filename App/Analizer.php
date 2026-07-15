<?php

namespace App;

use Core\Request;

class Analizer
{
    public function __construct(
        public \PDO $pdo, 
        public Request $request
    )
    {}

    public function run()
    {
        $inputs = $this->request->getParams();
        
        $sql = 'SELECT * FROM users WHERE 1=1 ';
        $params = [];

        $map_rules = [
            'country' => 'equals',
            'city' => 'equals',
            'is_active' => 'equals',
            'gender' => 'equals',
            'has_children' => 'equals',
            'family_status' => 'equals',
            'salary' => 'range',
            'birth_date' => 'range',
            'registration_date' => 'range'
        ];
        
        foreach ($map_rules as $column => $type){
            switch ($type){
                case 'equals':
                    if(isset($inputs[$column]) AND $inputs[$column] !== ''){
                        $sql .= " AND $column = :$column ";
                        $params[':' . $column] = $inputs[$column];
                        break;
                    }                   
                
                case 'range':
                    $fromKey = $column . '_from';
                    $toKey = $column . '_to';
                    
                    if (!empty($inputs[$fromKey])){
                        $sql .= " AND $column >= :$fromKey ";
                        $params[':' . $fromKey] = $inputs[$fromKey];
                    }if(!empty($inputs[$toKey])){
                        $sql .= " AND $column <= :$toKey ";
                        $params[':' . $toKey] = $inputs[$toKey];                        
                    }
                    break;
            }
        }
        $stm = $this->pdo->prepare($sql);
        $users = $stm->execute($params);
        $users = $stm->fetchAll(\PDO::FETCH_OBJ);
        
        require __DIR__ . '/views/analize.php';
    }
}
