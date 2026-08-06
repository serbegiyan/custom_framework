<?php

namespace App\Policies;

use App\Services\OrganizationService;
use App\ValueObjects\OrganizationId;

class OrganizationPolicy
{
    public function __construct(
        public OrganizationService $service,
    ) {
    }

    private function isOwner(int $user_id, OrganizationId $org_id): bool
    {
        $ownerId = $this->service->getOwnerId($org_id->orgId);
        return (int)$user_id === (int)$ownerId;
    }

    public function update(int $user_id, OrganizationId $org_id): bool
    {
        return $this->isOwner($user_id, $org_id);
    }

    public function delete(int $user_id, OrganizationId $org_id): bool
    {
        return $this->isOwner($user_id, $org_id);
    }
}
