<?php

namespace Tests\Unit;

use App\Policies\OrganizationPolicy;
use App\Services\OrganizationService;
use App\ValueObjects\OrganizationId;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(OrganizationPolicy::class)]
class OrganizationPolicyTest extends TestCase
{
    public function testIfGotAccess(): void
    {
        $orgId = new OrganizationId(2);
        $service = $this->createMock(OrganizationService::class);

        $service->expects($this->once())
            ->method('getOwnerId')
            ->willReturn(5);      
        
        $policy = new OrganizationPolicy($service);
        $result = $policy->update(5, $orgId);

        $this->assertTrue($result);
    }
    
    public function testUpdateReturnsFalseIfUserIsNotOwner(): void
    {
        $orgId = new OrganizationId(2);
        $service = $this->createMock(OrganizationService::class);

        $service->expects($this->once())
            ->method('getOwnerId')
            ->willReturn(5);      
        
        $policy = new OrganizationPolicy($service);
        $result = $policy->update(10, $orgId);

        $this->assertFalse($result);
    }
}