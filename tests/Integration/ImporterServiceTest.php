<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Services\ImporterService;
use App\ValueObjects\OrganizationId;
use App\Repositories\StatisticRepository;
use App\Services\CsvReader;
use App\Validators\CsvValidator;
use Core\Database;
use RuntimeException;
use App\Interfaces\DatabaseInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Exceptions\ValidationException;

#[CoversClass(ImporterService::class)]
class ImporterServiceTest extends TestCase
{
    private CsvValidator $validator;
    private StatisticRepository $statRep;
    private CsvReader $reader;
    private Database $db;
    private string $path;

    public function setUp(): void
    {
        parent::setUp();
        $dsn = $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
        $data = new Database($dsn, $user, $password);
        $this->db = $data;
        $this->path = __DIR__ . '/../storage/importer.csv';

        $this->db->execute('TRUNCATE TABLE statics RESTART IDENTITY CASCADE', []);
        $this->db->execute('TRUNCATE TABLE organizations RESTART IDENTITY CASCADE', []);
        
        $this->statRep = new StatisticRepository($this->db);
        $this->reader = new CsvReader();
        $this->validator = new CsvValidator();        
    }

    protected function tearDown(): void
    {
        $sql = 'DELETE FROM statics';
        $this->db->execute($sql, []);
        $sqlOrg = 'DELETE FROM organizations';
        $this->db->execute($sqlOrg, []);
        parent::tearDown();
    }

    private function createCorrectFile(int $rowsCount): void
    {
        $fp = fopen($this->path, 'w');
        $headers = ['country', 'city', 'is_active', 'gender', 'birth_date',
        'salary', 'has_children', 'family_status', 'registration_date'];
        $this->assertIsResource($fp);
        fputcsv($fp, $headers);
        $rows = ['Brazil', 'Okeyview', '1', 'female', '2014-10-28', 80930, '1', 'divorced', '2022-07-18'];
        for($i = 0; $i < $rowsCount; $i++){
            fputcsv($fp, $rows);
        }
        fclose($fp);
    }

    private function createIncorrectFile(): void
    {
        $fp = fopen($this->path, 'w');
        $headers = ['country', 'city', 'is_active', 'gender', 'birth_date',
        'salary', 'has_children', 'family_status', 'registration_date'];
        $this->assertIsResource($fp);
        fputcsv($fp, $headers);
        $row1 = ['Brazil', 'Okeyview', '1', 'female', '2014-10-28', 80930, '1', 'divorced', '2022-07-18'];
        fputcsv($fp, $row1);
        $row2 = ['Brazil', '1', 000, '2014-10-28', 80930, '1', 'error'];
        fputcsv($fp, $row2);
        $row3 = ['Brazil', 'Okeyview', '1', 'female', '2014-10-28', 80930, '1', 'divorced', '2022-07-18'];
        fputcsv($fp, $row3);
        fclose($fp);
    }

    public function testIfFileDoesNotExsist(): void
    {
        $importer = new ImporterService($this->validator, $this->statRep, $this->reader);
        $orgId = new OrganizationId(1);
        $this->expectException(ValidationException::class);
        $importer->import($orgId, null, 3);
    }

    public function testIfCorrectlyHandleInvalidRows(): void
    {
        $sqlOrg = "INSERT INTO organizations (name) VALUES ('Innowise')";
        $this->db->execute($sqlOrg, []);

        $orgId = new OrganizationId(1);
        $importer = new ImporterService($this->validator, $this->statRep, $this->reader);
        $this->createIncorrectFile();
        $importer->import($orgId, $this->path, 3);        

        $sql = 'SELECT * FROM statics';
        $result = $this->db->select($sql, []);

        $this->assertCount(2, $result);
    }

    #[DataProvider('filterProvider')]
    public function testIfImportRunCorrect(int $rowsCount, int $chankSize, int $expectedCount): void
    {
        $sqlOrg = "INSERT INTO organizations (name) VALUES ('Innowise')";
        $this->db->execute($sqlOrg, []);

        $orgId = new OrganizationId(1);
        $importer = new ImporterService($this->validator, $this->statRep, $this->reader);
        $this->createCorrectFile($rowsCount);
        $importer->import($orgId, $this->path, $chankSize);        

        $sql = 'SELECT * FROM statics';
        $result = $this->db->select($sql, []);

        $this->assertCount($expectedCount, $result);
    }

    /**
     * @return array<string, array<string, int>>
     */
    public static function filterProvider(): array
    {
        return [
            'Меньше чанка' => [
                'rowsCount' => 2,
                'chankSize' => 3,
                'expectedCount' => 2
            ],
            'Ровно чанк' => [
                'rowsCount' => 3,
                'chankSize' => 3,
                'expectedCount' => 3
            ],
            'Чанк + хвостик' => [
                'rowsCount' => 4,
                'chankSize' => 3,
                'expectedCount' => 4
            ],
            'Два чанка + хвостик' => [
                'rowsCount' => 7,
                'chankSize' => 3,
                'expectedCount' => 7
            ],
        ];
    }

}