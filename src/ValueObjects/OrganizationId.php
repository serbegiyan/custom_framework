<?php

namespace App\ValueObjects;

class OrganizationId
{
    public function __construct(
        public readonly int $orgId,
    ) {
        if ($this->orgId < 0) {
            throw new \InvalidArgumentException('Invalid organization ID');
        }
    }
}
