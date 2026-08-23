<?php

namespace Tests\Unit;

use App\Repositories\StatisticRepository;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Interfaces\DatabaseInterface;
use App\ValueObjects\OrganizationId;
use App\Exceptions\ValidationException;

#[CoversClass(StatisticRepository::class)]
class StatisticRepositoryTest extends TestCase
{
    public function testIfArrayEmpty(): void
    {
        $db = $this->createStub(DatabaseInterface::class);
        $OrganizationId = new OrganizationId(1);
        $repo = new StatisticRepository($db);
        $this->expectException(ValidationException::class);
        $repo->insertBatch([], $OrganizationId->orgId);
    }

    public function testIfSqlCreatedCorrect(): void
    {
        $OrganizationId = new OrganizationId(1);
        $db = $this->createMock(DatabaseInterface::class);
        $db->expects($this->once())
            ->method('execute')
            ->with(
                $this->stringContains('INSERT INTO statics'), 
                $this->callback(function (array $params) use ($organizationId) {
                    if (count($params) !== 9) { 
                        return false;
                    }
                    if ($params[2] !== 1) {
                        return false;
                    }
                    return true;            
                })
            );        
        $chank = [['France', 'Paris'], ['USA', 'NY'], ['Belorus', 'Minsk']];
        $repo = new StatisticRepository($db);
        $repo->insertBatch($chank, $OrganizationId->orgId);
    }
}