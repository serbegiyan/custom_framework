<?php

namespace App\Interfaces;

interface ResponseInterface
{
    public function getBody(): string;
    public function getStatusCode(): int;
    public function getHeaders(): array;
}
