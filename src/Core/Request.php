<?php

namespace Core;

class Request
{
    private ?int $user_id = null;

    public function getPath(): string
    {
        $temUrl = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri = empty($temUrl) ? '' : $temUrl;
        return $uri !== '/' ? rtrim($uri, '/') : '/';
    }

    public function getFiles(string $key): string|null
    {
        if (isset($_FILES[$key]) and $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
            $tmp_file = $_FILES[$key]['tmp_name'];
            return $tmp_file;
        }
        return null;
    }

    public function isValidSize(string $key): bool
    {
        if (isset($_FILES[$key]) and $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
            $size = $_FILES[$key]['size'];
            return $size <= 5242880;   // 5 MB
        }
        return false;
    }

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /** @return array<int|string, array<mixed>|string>*/
    public function getParams(): array
    {
        if ($this->getMethod() == 'GET') {
            return $_GET;
        }
        if ($this->getMethod() == 'POST') {
            return $_POST;
        }
        if (in_array($this->getMethod(), ['PATCH', 'PUT', 'DELETE'])) {
            $data = file_get_contents('php://input');
            if (!$data) {
                throw new \RuntimeException('Invalid inputs');
            }
            $patchData = [];
            parse_str($data, $patchData);
            return $patchData;
        }
        return [];
    }

    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getString(string $key, string $default = ''): string
    {
        $params = $this->getParams();
        $value = $params[$key] ?? $default;

        return is_array($value) ? $default : (string)$value;
    }

    public function getInt(string $key, int $default = 0): int
    {
        $params = $this->getParams();
        $value = $params[$key] ?? null;

        if ($value === null || is_array($value)) {
            return $default;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        return $filtered !== false ? $filtered : $default;
    }

    public function getCookies(string $key): ?string
    {
        return $_COOKIE[$key] ?? null;
    }
}
