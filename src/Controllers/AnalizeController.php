<?php

namespace App\Controllers;

use App\Core\Localization;
use App\Core\Request;
use App\Exceptions\ForbiddenException;
use App\Interfaces\DatabaseInterface;
use App\Services\AnalizerService;
use App\Services\OrganizationService;

class AnalizeController
{
    public function __construct(
        public DatabaseInterface $db,
        public Request $request,
        public AnalizerService $analizer,
        public Localization $local,
        public OrganizationService $orgService
    ) {
    }

    public function index(): void
    {
        $user_id = $this->request->getUserId();
        $organizationId = $this->orgService->getOrgId((int)$user_id);
        if (!$organizationId) {
            throw new ForbiddenException($this->local->translate('auth.forbidden'));
        }
        $filter = $this->request->getParams();
        $statics = $this->analizer->run($filter, $organizationId);
        $this->render('analize', [
            'statics' => $statics,
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
