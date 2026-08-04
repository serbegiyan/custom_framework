<?php

namespace App\Core;

use App\Interfaces\ResponseInterface;

class JsonResponse implements ResponseInterface
{
    /**
     * @param array<array-key, mixed> $data
     * @param int $statusCode
     */
    public function __construct(
        public array $data,
        public int $statusCode = 200,
    ) {
    }
    /**
     * @var array<string, string>
     */
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

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
