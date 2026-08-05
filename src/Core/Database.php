<?php

namespace App\Core;

use App\Interfaces\DatabaseInterface;
use PDO;

class Database implements DatabaseInterface
{
    private PDO $pdo;

    public function __construct(
        public string $dsn,
        public string $user,
        public string $password
    ) {
        $this->pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    /**
    * @param array<string, mixed> $params
    * @param class-string|null $className
    * @return array<int, array<string, mixed>|object>
    */
    public function select(string $sql, array $params, ?string $className = null): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        if ($className) {
            return $stmt->fetchAll(PDO::FETCH_CLASS, $className);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
    *   @param array<string, mixed> $params
    *   @return array<string, mixed>|false
    */
    //пока не реализовано
    public function find(string $sql, array $params): array|false
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetch();
        return $data;
    }

    /**
     * @param array<string|int, mixed> $params
     */
    public function execute(string $sql, array $params): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $count = $stmt->rowCount();
        return $count;
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }

    public function inTransaction(): void
    {
        $this->pdo->inTransaction();
    }
}
