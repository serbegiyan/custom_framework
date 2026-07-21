<?php

namespace App\Core\Interfaces;

interface DatabaseInterface
{
    public function select(string $sql, array $params, ?string $className = null): array;
    public function find(string $sql, array $params): array|false;
    public function execute(string $sql, array $params): int;
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollBack(): void;
}