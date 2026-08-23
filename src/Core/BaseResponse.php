<?php

namespace Core;

use App\Interfaces\ResponseInterface;

abstract class BaseResponse implements ResponseInterface
{
    /**
    * @var array<string, string>
    */
    protected array $headers = [];

    /**
     * @var array<string, array{
     *     value: string,
     *     options: array{
     *         expires?: int,
     *         path?: string,
     *         domain?: string,
     *         secure?: bool,
     *         httponly?: bool,
     *         samesite?: 'Lax'|'Strict'|'None'
     *     }
     * }>
     */
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

    /**
     * @return array<string, array{
     *     value: string,
     *     options: array{
     *         expires?: int,
     *         path?: string,
     *         domain?: string,
     *         secure?: bool,
     *         httponly?: bool,
     *         samesite?: 'Lax'|'Strict'|'None'
     *     }
     * }>
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    abstract public function getBody(): string;
}
