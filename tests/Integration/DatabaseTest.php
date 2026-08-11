<?php

namespace Tests\Integration;

use Core\Database;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Database::class)]
class DatabaseTest extends TestCase
{
    private Database $db;

    public function setUp(): void
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

    public function testItCanInsertAndSelectData(): void
    {
        $userSql = "INSERT INTO users (name, email, password_hash)
        VALUES (?, ?, ?)";

        $password = password_hash('12345678', PASSWORD_BCRYPT);

        $this->db->execute($userSql, ['testUser', 'test@test.com', $password]);

        $select = "SELECT name FROM users WHERE email = ?";

        $user = $this->db->select($select, ['test@test.com']);

        $name = $user[0]['name'];
        
        $this->assertSame('testUser', $name);
    }
}