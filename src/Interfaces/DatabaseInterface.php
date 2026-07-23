<?php

namespace App\Interfaces;

interface DatabaseInterface
{
    /**
    *   @param array<string, mixed> $params
    * @return array<int, array<string, mixed>|object>
    */
    public function select(string $sql, array $params, ?string $className = null): array;
    /**
    *   @param array<string, mixed> $params
    *   @return array<string, mixed>|false
    */
    public function find(string $sql, array $params): array|false;
    /**
     * @param array<string|int, mixed> $params
     */
    public function execute(string $sql, array $params): int;
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollBack(): void;
}
