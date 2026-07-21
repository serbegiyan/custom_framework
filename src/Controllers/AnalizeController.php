<?php

namespace App\Controllers;

use App\Core\Request;
use App\Services\AnalizerService;
use App\Core\Interfaces\DatabaseInterface;

class AnalizeController
{
    public function __construct(
        public DatabaseInterface $db,
        public Request $request,
        public AnalizerService $analizer
    ) {
    }

    public function index(): void
    {
        $filter = $this->request->getParams();
        $users = $this->analizer->run($filter, $this->db);
        $this->render('analize', [
            'users' => $users
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function render(string $view, array $data): void
    {
        extract($data);
        
        require __DIR__ . '/../../views/' . $view . '.php';
    }
}
