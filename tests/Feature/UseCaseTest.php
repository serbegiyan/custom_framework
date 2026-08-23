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
use App\Services\CsvReader;
use App\Repositories\StatisticRepository;

#[CoversClass(ImportUseCase::class)]
class UseCaseTest extends TestCase
{
    private Database $db;
    private CsvValidator $validator;
    private OrganizationId $orgId;
    private AnalizerService $analizer;
    private CsvReader $reader;
    private StatisticRepository $repo;
    private ImporterService $importer;
    private ImportUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $dsn = $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];

        $data = new Database($dsn, $user, $password);
        $this->db = $data;  
        $this->validator = new CsvValidator();
        $this->orgId = new OrganizationId(1);
        $this->analizer = new AnalizerService($this->db);
        $this->reader = new CsvReader();
        $this->repo = new StatisticRepository($this->db);
        $this->importer = new ImporterService($this->validator, $this->repo, $this->reader);        
        $this->useCase = new ImportUseCase($this->db, $this->analizer, $this->importer);      
    }

    protected function tearDown(): void
    {
        $sql = 'DELETE FROM statics';
        $this->db->execute($sql, []);
        parent::tearDown();
    }

    public function testIfTransactionRunCorrect(): void
    {   
        $path = __DIR__ . '/../storage/test.csv';
        $result = $this->useCase->runTransaction($this->orgId, $path);

        $this->assertCount(1, $result['skippedRows']);
        $this->assertArrayHasKey(3, $result['skippedRows']);
        $this->assertStringContainsString('Строка 3:', $result['skippedRows'][3]);

        $sql = 'SELECT country FROM statics';
        
        $statics = $this->db->select($sql, []);
        $this->assertCount(2, $statics);
    }

    public function testIfRollbackRunCorrectWhenFileIsBroken(): void
    {  
        try{
            $result = $this->useCase->runTransaction($this->orgId, 'WrongPath');
            $this->fail('Ожидалось исключение RuntimeException, но метод завершился успешно.');
        }catch(RuntimeException $e){
        }

        $sql = 'SELECT country FROM statics';
        
        $statics = $this->db->select($sql, []);
        $this->assertCount(0, $statics);
    }
}