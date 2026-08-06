<?php

namespace App\Controllers;

use Core\HtmlResponse;
use Core\Localization;
use Core\Request;
use Core\View;
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
        public OrganizationService $orgService,
        public View $view,
    ) {
    }

    public function index(): HtmlResponse
    {
        $user_id = $this->request->getUserId();
        $organizationId = $this->orgService->getOrgId((int)$user_id);
        if (!$organizationId) {
            throw new ForbiddenException($this->local->translate('auth.forbidden'));
        }
        $filter = $this->request->getParams();
        $statics = $this->analizer->run($filter, $organizationId);
        $responseString = $this->view->render('analize', [
            'statics' => $statics,
        ]);
        return new HtmlResponse($responseString, 200);
    }
}
