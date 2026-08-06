<?php

namespace App\Services;

use Core\Localization;
use Core\Request;
use App\Exceptions\ForbiddenException;
use App\Interfaces\ContainerInterface;
use App\ValueObjects\OrganizationId;

class Gate
{
    public function __construct(
        public ContainerInterface $container,
        public Request $request,
        public Localization $local,
    ) {
    }

    public function authorize(string $policy, string $method, OrganizationId $org_id): void
    {
        $user_id = $this->request->getUserId();
        $policyClass = $this->container->get($policy);
        $access = $policyClass->$method($user_id, $org_id);
        if (!$access) {
            throw new ForbiddenException($this->local->translate('auth.forbidden'));
        }
    }
}
