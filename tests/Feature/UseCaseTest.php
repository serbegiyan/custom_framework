<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\UseCases\ImportUseCase;
use App\Interfaces\DatabaseInterface;
use Core\Database;
use App\Services\AnalizerService;
use App\Services\ImporterService;
use App\ValueObjects\OrganizationId;
use App\Validators\CsvValidator;
use App\Exceptions\ValidationException;
use RuntimeException;

#[CoversClass(ImportUseCase::class)]
class UseCaseTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        $sql = 'DELETE FROM statics';
        $this->db->execute($sql, []);
        parent::tearDown();
    }

    public function testIfTransactionRunCorrect(): void
    {   
        $validator = new CsvValidator();
        $analizer = new AnalizerService($this->db);
        $importer = new ImporterService($validator, $this->db);
        $orgId = new OrganizationId(1);
        $path = __DIR__ . '/../storage/test.csv';
        $useCase = new ImportUseCase($this->db, $analizer, $importer);
        $result = $useCase->runTransaction($orgId, $path);

        $this->assertCount(1, $result['skippedRows']);
        $this->assertArrayHasKey(3, $result['skippedRows']);
        $this->assertStringContainsString('Строка 3:', $result['skippedRows'][3]);

        $sql = 'SELECT country FROM statics';
        
        $statics = $this->db->select($sql, []);
        $this->assertCount(2, $statics);
    }

    public function testIfRollbackRunCorrectWhenFileIsBroken(): void
    {
        $validator = new CsvValidator();
        $analizer = new AnalizerService($this->db);
        $importer = new ImporterService($validator, $this->db);
        $orgId = new OrganizationId(1);
        $useCase = new ImportUseCase($this->db, $analizer, $importer);

        try{
            $result = $useCase->runTransaction($orgId, 'WrongPath');
            $this->fail('Ожидалось исключение RuntimeException, но метод завершился успешно.');
        }catch(RuntimeException $e){
        }

        $sql = 'SELECT country FROM statics';
        
        $statics = $this->db->select($sql, []);
        $this->assertCount(0, $statics);
    }
}