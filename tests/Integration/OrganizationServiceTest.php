<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Services\OrganizationService;
use App\Interfaces\DatabaseInterface;
use App\ValueObjects\OrganizationId;
use App\Exceptions\ValidationException;
use Core\Database;

#[CoversClass(OrganizationService::class)]
class OrganizationServiceTest extends TestCase
{
    private Database $db;

    protected function setUp(): void
    {
        parent::setUp();
        $dsn = $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
        $data = new Database($dsn, $user, $password);
        $this->db = $data;

        $this->db->execute('TRUNCATE TABLE organizations RESTART IDENTITY CASCADE', []);
        $this->db->execute('TRUNCATE TABLE users RESTART IDENTITY CASCADE', []);

        $userSql = "INSERT INTO users (name, email, password_hash)
        VALUES (?, ?, ?)";
        $password = password_hash('12345678', PASSWORD_BCRYPT);
        $this->db->execute($userSql, ['testUser', 'test@test.com', $password]);
    }

    protected function tearDown(): void
    {
        $this->db->execute('DELETE FROM organizations', []);
        $this->db->execute('DELETE FROM users', []);        
        parent::tearDown();
    }

    public function testIfStoreToBdCorrect(): void
    {
        $service = new OrganizationService($this->db);
        $service->storeToDb('Google', 1);
        $sql = 'SELECT * FROM organizations';
        $res = $this->db->select($sql, []);

        $this->assertIsArray($res[0]);
        $this->assertCount(1, $res);        
        $this->assertSame('Google', $res[0]['name']);
        $this->assertSame(1, $res[0]['owner_id']);
    }

    public function testIfUpdateAndDeleteWorkCorrect(): void
    {
        $service = new OrganizationService($this->db);
        $service->storeToDb('Google', 1);
        $sql = 'SELECT id FROM organizations WHERE name = :name';
        $orgId = $this->db->select($sql, [':name' => 'Google']);

        $this->assertIsArray($orgId[0]);
        $id = (int)$orgId[0]['id'];

        $service->updateToBd('NewName', $id);
        $sqlId = 'SELECT name FROM organizations WHERE id = :id';

        
        $newOrg = $this->db->select($sqlId, [':id' => $id]);
        $this->assertIsArray($newOrg[0]);
        $this->assertSame('NewName', $newOrg[0]['name']);

        $service->deleteFromBd($id);
        $deleted = $this->db->select($sql, [':name' => 'NewName']);
        $this->assertSame([], $deleted);        
    }

    public function testIfOrganizationIdAndOwnerIdAndOrgListGetCorrect(): void
    {
        $userSql = "INSERT INTO users (name, email, password_hash)
        VALUES (?, ?, ?)";
        $password = password_hash('12345678', PASSWORD_BCRYPT);
        $this->db->execute($userSql, ['User2', 'test2@test.com', $password]);
        $service = new OrganizationService($this->db);
        $service->storeToDb('Google', 1);
        $service->storeToDb('Netflix', 1);
        $service->storeToDb('Microsoft', 2);

        //Check getting OrganizationId
        $org = $service->getOrgId(2);
        $this->assertNotNull($org);
        $org_id = $org->orgId;
        $this->assertSame(3, $org_id);

        //Check getting OwnerId
        $owner_id = $service->getOwnerId(3);
        $this->assertSame(2, $owner_id);

        //Check getting Organization List
        $list = $service->getOrgList(1);
        $this->assertIsArray($list[0]);
        $this->assertIsArray($list[1]);
        $orgList = [$list[0]['name'], $list[1]['name']];
        $this->assertSame(['Google', 'Netflix'], $orgList);
    }
}