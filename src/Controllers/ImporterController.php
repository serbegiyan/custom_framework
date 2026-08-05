<?php

namespace App\Controllers;

use App\Core\HtmlResponse;
use App\Core\Localization;
use App\Core\Request;
use App\Core\View;
use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;
use App\Interfaces\DatabaseInterface;
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
        public View $view,
        public DatabaseInterface $db,
    ) {
    }

    public function store(): HtmlResponse
    {
        if (!$this->request->isValidSize('csv_file')) {
            throw new ValidationException($this->local->translate('error_400'));
        }
        $file = $this->request->getFiles('csv_file');
        $user_id = $this->request->getUserId();
        $organizationId = $this->orgService->getOrgId((int)$user_id);
        if (!$organizationId) {
            throw new ForbiddenException($this->local->translate('auth.forbidden'));
        }
        $this->db->beginTransaction();
        try {
            $skippedRows = $this->importer->import($organizationId, $file);
            $this->db->commit();
            $statics = $this->analizer->run([], $organizationId);
            $html = $this->view->render('analize', [
                'statics' => $statics,
                'skipped_rows' => $skippedRows,
            ]);
            return new HtmlResponse($html, 200);
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            throw $e;
        }
    }
}
