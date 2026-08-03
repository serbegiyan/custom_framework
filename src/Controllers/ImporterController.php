<?php

namespace App\Controllers;

use App\Core\Localization;
use App\Core\Request;
use App\Exceptions\ForbiddenException;
use App\Services\AnalizerService;
use App\Services\ImporterService;
use App\Services\OrganizationService;

class ImporterController
{
    public function __construct(
        public Request $request,
        public ImporterService $importer,
        public AnalizerService $analizer,
        public OrganizationService $orgService,
        public Localization $local,
    ) {
    }

    public function store(): void
    {
        if ($this->request->isValidSize('csv_file')) {
            $file = $this->request->getFiles('csv_file');
            $user_id = $this->request->getUserId();
            $organizationId = $this->orgService->getOrgId((int)$user_id);
            if (!$organizationId) {
                throw new ForbiddenException($this->local->translate('auth.forbidden'));
            }
            $this->importer->import($organizationId, $file);
            $statics = $this->analizer->run([], $organizationId);
            require __DIR__ . '/../../views/analize.php';
        } else {
            echo $this->local->translate('error_400');
        }
    }
}
