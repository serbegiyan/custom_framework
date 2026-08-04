<?php

namespace App\Core;

use App\Interfaces\ResponseInterface;

class JsonResponse implements ResponseInterface
{
    public function __construct(
        public array $data,
        public int $statusCode = 200,
    ) {
    }

    public array $headers = ['Content-Type' => 'application/json; charset=utf-8'];

    public function getBody(): string
    {
        $data = json_encode($this->data, JSON_THROW_ON_ERROR);
        return $data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
