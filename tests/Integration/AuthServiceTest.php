<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Services\AuthService;
use App\Interfaces\DatabaseInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Services\Localization;
use App\ValueObjects\OrganizationId;
use App\Exceptions\ValidationException;
use Core\Database;

#[CoversClass(AuthService::class)]
class AuthServiceTest extends TestCase
{
    private Database $db;
    /**
     * @var \App\Services\Localization&\PHPUnit\Framework\MockObject\MockObject
     */
    private Localization $local;

    protected function setUp(): void
    {
        parent::setUp();
        $dsn = $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
        $data = new Database($dsn, $user, $password);
        $this->db = $data;
        $this->local = $this->createMock(Localization::class);
    }

    protected function tearDown(): void
    {
        $sql = 'DELETE FROM users';
        $this->db->execute($sql, []);
        parent::tearDown();
    }

    public function testIfRegistrationCorrect(): void
    {
        $this->local->method('translate')->willReturn('success');
        $service = new AuthService($this->db, $this->local);
        
        $user = $service->registerUser('Test', 'test@test.com', '12345678');
        $this->assertGreaterThan(0, $user);

        $sql = "SELECT email FROM users WHERE id = :id";
        $dbUser = $this->db->select($sql, [':id' => $user]);
        $this->assertIsArray($dbUser[0]);
        $this->assertSame('test@test.com', $dbUser[0]['email']);
    }

    public function testIfUserAlreadyExists(): void
    {
        $this->local->method('translate')->willReturn('already_exists');
        $service = new AuthService($this->db, $this->local);
        
        $user = $service->registerUser('Test', 'test@test.com', '12345678');

        $this->expectException(ValidationException::class);
        $user2 = $service->registerUser('Test2', 'test@test.com', '12345678');
    }

    public function testIfMissedInputsInRegistration(): void
    {
        $this->expectException(ValidationException::class);
        $this->local->method('translate')->willReturn('invalid');
        $service = new AuthService($this->db, $this->local);        
        $user = $service->registerUser('Test', '', '12345678');
    }

    public function testIfLoginCorrect(): void
    {
        $this->local->method('translate')->willReturn('success');
        $service = new AuthService($this->db, $this->local);
        
        $user = $service->registerUser('Test', 'test@test.com', '12345678');
        $userLogin = $service->loginUser('test@test.com', '12345678');
        $this->assertSame($user, $userLogin);
    }

    public function testIfInvalidInputsInlogin(): void
    {
        $this->local->method('translate')->willReturn('invalid');
        $service = new AuthService($this->db, $this->local);
        
        $user = $service->registerUser('Test', 'test@test.com', '12345678');

        $this->expectException(ValidationException::class);
        $userLogin = $service->loginUser('test@test.com', 'wrongPassword');
    }

    public function testIfMissedInputsInLogin(): void
    {
        $this->local->method('translate')->willReturn('invalid');
        $service = new AuthService($this->db, $this->local);
        
        $user = $service->registerUser('Test', 'test@test.com', '12345678');

        $this->expectException(ValidationException::class);
        $userLogin = $service->loginUser('test@test.com', '');
    }
}