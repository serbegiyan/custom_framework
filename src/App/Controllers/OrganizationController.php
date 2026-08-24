<?php

namespace App\Controllers;

use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;
use App\Policies\OrganizationPolicy;
use App\Services\Gate;
use App\Services\Localization;
use App\Services\OrganizationService;
use App\ValueObjects\OrganizationId;
use Core\JsonResponse;
use Core\Request;

class OrganizationController
{
    public function __construct(
        public Request $request,
        public OrganizationService $orgService,
        public Localization $local,
        public Gate $gate,
    ) {
    }

    public function index(): JsonResponse
    {
        $user_id = $this->request->getUserId();
        $orgs = $this->orgService->getOrgList((int)$user_id);
        return new JsonResponse($orgs, 200);
    }

    public function store(): JsonResponse
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
        return new JsonResponse(['message' => $this->local->translate('created')], 201);
    }

    public function update(): JsonResponse
    {
        $params = $this->request->getParams();
        if (!isset($params['id']) || empty($params['id'])) {
            throw new ValidationException('Organization ID is required');
        }
        $org_id = new OrganizationId((int) ($params['id'] ?? 0));
        $this->gate->authorize(OrganizationPolicy::class, 'update', $org_id);
        $orgName = $this->request->getString('name');
        if (!$orgName || trim($orgName) === '') {
            throw new ValidationException($this->local->translate('invalid.incorrect_value'));
        }
        $this->orgService->updateToBd($orgName, $org_id->orgId);
        return new JsonResponse(['message' => $this->local->translate('success')], 200);
    }

    public function delete(): JsonResponse
    {
        $params = $this->request->getParams();
        $org_id = new OrganizationId((int) ($params['id'] ?? 0));
        if (!isset($params['id']) || empty($params['id'])) {
            throw new ValidationException('Organization ID is required');
        }
        $this->gate->authorize(OrganizationPolicy::class, 'delete', $org_id);
        $this->orgService->deleteFromBd($org_id->orgId);
        return new JsonResponse([], 204);
    }
}
