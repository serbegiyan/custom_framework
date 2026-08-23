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
    public function withHeader(string $name, string $value): static;

    public function withCookie(string $name, string $value, array $options): static;
    public function getCookies(): array;
}
