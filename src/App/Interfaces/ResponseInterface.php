<?php

namespace App\Interfaces;

interface ResponseInterface
{
    public function getBody(): string;
    public function getStatusCode(): int;
    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;
}
