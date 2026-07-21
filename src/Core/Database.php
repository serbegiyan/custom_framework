<?php

namespace App\Core;

use App\Core\Interfaces\DatabaseInterface;
use PDO;

class Database implements DatabaseInterface
{
   
    public function __construct(
        public string $dsn,
        public string $user,
        public string $password
    )
    {
        $this->pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    private PDO $pdo;

    /** 
    *   @param array<string, mixed> $params
    */
    public function select(string $sql, array $params, ?string $className = null): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        if($className){
            $stmt->setFetchMode(PDO::FETCH_CLASS, $className);
        }
        $data = $stmt->fetchAll();
        return $data;
    }

    /** 
    *   @param array<string, mixed> $params
    */
    public function find(string $sql, array $params): array|false
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetch();
        return $data;
    }

    /** 
    *   @param array<string, mixed> $params
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

}
