<?php

namespace App\Controllers;

use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;
use App\Services\Localization;
use App\Services\OrganizationService;
use App\UseCases\ImportUseCase;
use Core\HtmlResponse;
use Core\Request;
use Core\View;

class ImporterController
{
    public function __construct(
        public Request $request,
        public OrganizationService $orgService,
        public Localization $local,
        public View $view,
        public ImportUseCase $useCase,
    ) {
    }

    public function store(): HtmlResponse
    {
        if (!$this->request->isValidSize('csv_file')) {
            throw new ValidationException($this->local->translate('error_400'));
        }
        $file = $this->request->getFiles('csv_file');
        if (!$file) {
            throw new ValidationException($this->local->translate('error_400'));
        }
        $user_id = $this->request->getUserId();
        $organizationId = $this->orgService->getOrgId((int)$user_id);
        if (!$organizationId) {
            throw new ForbiddenException($this->local->translate('auth.forbidden'));
        }
        $data = $this->useCase->runTransaction($organizationId, $file);
        $html = $this->view->render('analize', $data);
        return new HtmlResponse($html, 200);
    }
}
