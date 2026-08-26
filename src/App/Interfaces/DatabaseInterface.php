<?php

namespace App\Interfaces;

interface DatabaseInterface
{
    /**
    * @param array<string|int, mixed> $params
    * @return array<int, array<string, mixed>|object>
    */
    public function select(string $sql, array $params, ?string $className = null): array;
    /**
     * @param array<string|int, mixed> $params
     */
    public function execute(string $sql, array $params): int;
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollBack(): void;
    public function inTransaction(): void;
}
