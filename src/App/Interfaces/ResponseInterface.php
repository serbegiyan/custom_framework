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

    /**
     * @param array{
    *     expires?: int,
    *     path?: string,
    *     domain?: string,
    *     secure?: bool,
    *     httponly?: bool,
    *     samesite?: 'Lax'|'Strict'|'None'
    * } $options
     */
    public function withCookie(string $name, string $value, array $options): static;

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
    public function getCookies(): array;
}
