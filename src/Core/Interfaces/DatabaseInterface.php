<?php

namespace App\Core\Interfaces;

interface DatabaseInterface
{
    /**
    *   @param array<string, mixed> $params
    *   @return array<string, mixed>
    */
    public function select(string $sql, array $params, ?string $className = null): array;
    /**
    *   @param array<string, mixed> $params
    *   @return array<string, mixed>|false
    */
    public function find(string $sql, array $params): array|false;
    /**
    *   @param array<string, mixed> $params
    */
    public function execute(string $sql, array $params): int;
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollBack(): void;
}
