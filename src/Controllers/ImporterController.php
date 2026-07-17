<?php

namespace App\Controllers;

use App\Services\ImporterService;
use App\Core\Request;

class ImporterController
{
    public function __construct(
        public \PDO $pdo,
        public Request $request,         
    )
    {}

    public function store()
    {
        $file = $this->request->getFiles('csv_file');
        $importer = new ImporterService();
        $importer->import($this->pdo, $file);
    }
}