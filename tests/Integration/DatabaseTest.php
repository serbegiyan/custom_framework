<?php

namespace Tests\Integration;

use Core\Database;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Database::class)]
class DatabaseTest extends TestCase
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
        $this->db->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->db->rollBack();
        parent::tearDown();
    }

    private function createUser(): void
    {
        $userSql = "INSERT INTO users (name, email, password_hash)
        VALUES (?, ?, ?)";

        $password = password_hash('12345678', PASSWORD_BCRYPT);

        $this->db->execute($userSql, ['testUser', 'test@test.com', $password]);
    }

    /**    
    * @return array<int, array<string, mixed>|object>
    */
    private function selectUser(): array
    {
        $select = "SELECT name FROM users WHERE email = :email";

        $user = $this->db->select($select, [':email' => 'test@test.com']);

        return $user;
    }

    public function testItCanInsertAndSelectData(): void
    {
        $this->createUser();

        $user = $this->selectUser();

        /** @var array<int, array<string, mixed>> $user */
        $name = $user[0]['name'];
        
        $this->assertSame('testUser', $name);
    }

    public function testItCanUpdateData(): void
    {
        $this->createUser();

        $update = "UPDATE users SET name = ? WHERE email = ?";

        $updatedCount = $this->db->execute($update, ['newName', 'test@test.com']);

        $this->assertSame(1, $updatedCount);

        $user = $this->selectUser();

        /** @var array<int, array<string, mixed>> $user */
        $name = $user[0]['name'];

        $this->assertSame('newName', $name);
    }

    public function testItCanDeleteData(): void
    {
        $this->createUser();

        $sql = 'DELETE FROM users WHERE email = ?';

        $this->db->execute($sql, ['test@test.com']);

        $user = $this->selectUser();

        $this->assertSame([], $user);
    }

    public function testItThrowsExceptionOnInvalidSql(): void
    {
        $this->expectException(\PDOException::class);

        $selectError = "SELECT_ERROR name FROM users WHERE email = :email";

        $user = $this->db->select($selectError, [':email' => 'test@test.com']);
    }
}