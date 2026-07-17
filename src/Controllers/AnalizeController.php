<?php

namespace App\Controllers;

use App\Core\Request;
use App\Services\AnalizerService;

class AnalizeController
{
    public function __construct(
        public \PDO $pdo,
        public Request $request,         
    )
    {}

    public function index()
    {
        $filter = $this->request->getParams();
        $service = new AnalizerService();
        $users = $service->run($filter, $this->pdo);

        require __DIR__ . '/../../views/analize.php';
    }
}