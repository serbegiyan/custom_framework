<?php

namespace App\Core;

class Session
{
    public function __construct()
    {
        session_name('MySession');
        if(session_status() === PHP_SESSION_NONE){
            session_start([
                'cookie_lifetime' => 86400,
                'cookie_httponly' => true,
            ]);
        }
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        $_SESSION = array();
        session_destroy();
    }
}