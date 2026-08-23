<?php

namespace Core;

use App\Interfaces\ResponseInterface;

abstract class BaseResponse implements ResponseInterface
{
    protected array $headers = [];
    protected array $cookies = [];

    public function __construct(
        protected int $statusCode = 200
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function withHeader(string $name, string $value): static
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function withCookie(string $name, string $value, array $options = []): static
    {
        $this->cookies[$name] = [
            'value' => $value,
            'options' => $options
        ];
        return $this;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    abstract public function getBody(): string;
}
