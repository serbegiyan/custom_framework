<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Services\AnalizerService;
use App\Interfaces\DatabaseInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use App\ValueObjects\OrganizationId;
use Core\Database;

#[CoversClass(AnalizerService::class)]
class AnalizerServiceTest extends TestCase
{
    private Database $db;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $dsn = $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
        $data = new Database($dsn, $user, $password);
        $this->db = $data;

        $this->db->execute('TRUNCATE TABLE statics RESTART IDENTITY CASCADE', []);
        $this->db->execute('TRUNCATE TABLE organizations RESTART IDENTITY CASCADE', []);
        
        $sqlOrg = "INSERT INTO organizations (name) VALUES ('Innowise'), ('EPAM')";
        $this->db->execute($sqlOrg, []);

        $sql = "INSERT INTO statics (country, city, is_active, gender, has_children, 
        family_status, salary, birth_date, registration_date, organization_id) 
            VALUES 
            ('France', 'Paris', true, 'male', true, 'married', 1000, '2007-04-05', '2020-04-03', 1),
            ('USA', 'NY', false, 'female', false, 'divorced', 2000, '2000-05-03', '2021-03-03', 1),
            ('Poland', 'Cracov', true, 'male', true, 'single', 3500, '2003-04-02', '2022-03-03', 2)
            ";
        $this->db->execute($sql, []);
        $this->service = new AnalizerService($this->db);
    }

    protected function tearDown(): void
    {
        $sql = 'DELETE FROM statics';
        $this->db->execute($sql, []);
        $sqlOrg = 'DELETE FROM organizations';
        $this->db->execute($sqlOrg, []);
        parent::tearDown();
    }

    #[DataProvider('filterProvider')]
    public function testIfFiltersReturnCorrectCount(array $filters, int $expectedCount): void
    {
        $orgId = new OrganizationId(1);
        $result = $this->service->run($filters ,$orgId);

        $this->assertCount($expectedCount, $result);
    }

    public static function filterProvider(): array
    {
        return [
            'пустые фильтры' => [
                'filters' => [],
                'expectedCount' => 2
            ],
            'фильтр equals по городу' => [
                'filters' => ['city' => 'Paris'],
                'expectedCount' => 1
            ],
            'фильтр range по зарплате (от)' => [
                'filters' => ['salary_from' => 1500],
                'expectedCount' => 1
            ],
            'несуществующий город' => [
                'filters' => ['city' => 'Minsk'],
                'expectedCount' => 0
            ],
        ];
    }
}