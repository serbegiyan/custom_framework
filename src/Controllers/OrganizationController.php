<?php

namespace App\Controllers;

use App\Core\Request;
use App\Services\OrganizationService;
use App\Core\Localization;
use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;
use App\Exceptions\InternalServerErrorException;
use App\Services\Gate;
use App\Policies\OrganizationPolicy;
use App\ValueObjects\OrganizationId;

class OrganizationController
{
    public function __construct(
        public Request $request,
        public OrganizationService $orgService,
        public Localization $local,
        public Gate $gate,
    ) {}

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
        $dataName = $this->request->getParams();
        $orgName = $dataName['name'] ?? '';
        if (!isset($dataName['name']) || trim($dataName['name']) === '') {
                throw new ValidationException($this->local->translate('invalid.incorrect_value'));    
            }
        $this->orgService->storeToDb($orgName, $user_id);
        echo $this->local->translate('success');         
    }

    public function update(): void
    {
        $org_Data = $this->request->getParams();
        $org_id = new OrganizationId ((int) ($org_Data['id'] ?? 0));
        $this->gate->authorize(OrganizationPolicy::class, 'update', $org_id);               
        $newData = $this->request->getParams();
        $newName = $newData['name'];
        if (!$newName || trim($newName) === '') {
            throw new ValidationException($this->local->translate('invalid.incorrect_value'));    
        }
        $this->orgService->updateToBd($newName, $org_id->orgId);
        echo $this->local->translate('success');            
    }

    public function delete(): void
    {
        $org_Data = $this->request->getParams();
        $org_id = new OrganizationId ((int) ($org_Data['id'] ?? 0));
        $this->gate->authorize(OrganizationPolicy::class, 'delete', $org_id); 
        $this->orgService->deleteFromBd($org_id->orgId);
        echo $this->local->translate('success');
    }
}
