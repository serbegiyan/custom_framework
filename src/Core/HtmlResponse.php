<?php

namespace App\Core;

use App\Interfaces\ResponseInterface;

class HtmlResponse implements ResponseInterface
{
    public function __construct(
        public string $body,
        public int $statusCode = 200,
    ) {
    }
    /**
     * @var array<string, string>
     */
    public array $headers = ['Content-Type' => 'text/html; charset=utf-8'];

    public function getBody(): string
    {
        return $this->body;
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
