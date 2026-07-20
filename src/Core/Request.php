<?php

namespace App\Core;

class Request
{
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


    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /** @return array<string, mixed>*/
    public function getParams(): array
    {
        if ($this->getMethod() == 'GET') {
            return $_GET;
        } else {
            return $_POST;
        }

    }
}
