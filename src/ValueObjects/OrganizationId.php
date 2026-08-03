<?php

namespace App\ValueObjects;

use App\Exceptions\ValidationException;

class OrganizationId
{
    public function __construct(
        public readonly int $orgId,
    ){
        if($this->orgId < 0){
            throw new \InvalidArgumentException('Invalid organization ID');
        }        
    }   
}