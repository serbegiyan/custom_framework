<?php

namespace App\Controllers;

use App\Core\Request;
use App\Services\AnalizerService;
use App\Services\ImporterService;
use PDO;

class ImporterController
{
    public function __construct(
        public PDO $pdo,
        public Request $request,
        public ImporterService $importer,
        public AnalizerService $analizer
    ) {
    }

    public function store(): void
    {
        if ($this->request->isValidSize('csv_file')) {
            $file = $this->request->getFiles('csv_file');
            $this->importer->import($this->pdo, $file);
            $users = $this->analizer->run([], $this->pdo);
            require __DIR__ . '/../../views/analize.php';
        } else {
            echo 'Error 400: Bad request';
        }
    }
}
