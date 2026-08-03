<?php

namespace App\Controllers;

use App\Core\Localization;
use App\Core\Request;
use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;
use App\Policies\OrganizationPolicy;
use App\Services\Gate;
use App\Services\OrganizationService;
use App\ValueObjects\OrganizationId;

class OrganizationController
{
    public function __construct(
        public Request $request,
        public OrganizationService $orgService,
        public Localization $local,
        public Gate $gate,
    ) {
    }

    public function index(): void
    {
        $user_id = $this->request->getUserId();
        $orgs = $this->orgService->getOrgList((int)$user_id);
        if (empty($orgs)) {
            echo $this->local->translate('not_available_orgs');
            return;
        }
        echo json_encode($orgs);
    }

    public function store(): void
    {
        $user_id = $this->request->getUserId();
        if (!$user_id) {
            throw new ForbiddenException($this->local->translate('auth.forbidden'));
        }
        $orgName = $this->request->getString('name');
        if (trim($orgName) === '') {
            throw new ValidationException($this->local->translate('invalid.incorrect_value'));
        }
        $this->orgService->storeToDb($orgName, $user_id);
        echo $this->local->translate('success');
    }

    public function update(): void
    {
        $org_Data = $this->request->getParams();
        $org_id = new OrganizationId((int) ($org_Data['id'] ?? 0));
        $this->gate->authorize(OrganizationPolicy::class, 'update', $org_id);
        $orgName = $this->request->getString('name');
        if (!$orgName || trim($orgName) === '') {
            throw new ValidationException($this->local->translate('invalid.incorrect_value'));
        }
        $this->orgService->updateToBd($orgName, $org_id->orgId);
        echo $this->local->translate('success');
    }

    public function delete(): void
    {
        $org_Data = $this->request->getParams();
        $org_id = new OrganizationId((int) ($org_Data['id'] ?? 0));
        $this->gate->authorize(OrganizationPolicy::class, 'delete', $org_id);
        $this->orgService->deleteFromBd($org_id->orgId);
        echo $this->local->translate('success');
    }
}
