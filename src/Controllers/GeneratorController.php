<?php

namespace App\Controllers;

use App\Services\GeneratorService;
use App\Core\Request;

class GeneratorController
{
    public function __construct(
        public \PDO $pdo,
        public Request $request,         
    )
    {}

    public $file = __DIR__ . '/../../storage/data.csv';

    public function generate()
    {
        $params = $this->request->getParams();
        $quantity = $params['quantity'];
        if(isset($quantity) and $quantity >= 1){
            $generator = new GeneratorService($this->file);            
            $generator->run($quantity);
        }else{
            echo 'Incorrect quantity';
        }
    }
}