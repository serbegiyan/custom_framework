<?php

namespace App\Controllers;

use App\Core\Request;
use App\Services\ImporterService;
use PDO;

class ImporterController
{
    public function __construct(
        public PDO $pdo,
        public Request $request,
    ) {
    }

    public function store(): void
    {
        $file = $this->request->getFiles('csv_file');
        $importer = new ImporterService();
        $importer->import($this->pdo, $file);
    }
}
