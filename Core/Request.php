<?php

namespace Core;

class Request
{
    public function getPath(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return $uri !== '/' ? rtrim($uri, '/') : '/';
    }

    public function getFiles(string $key)
    {
        if(isset($_FILES[$key]) AND $_FILES[$key]['error'] === UPLOAD_ERR_OK){
            $tmp_file = $_FILES[$key]['tmp_name'];
            return $tmp_file;
        }
        return null;
    }


    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function getParams(): array
    {
        if($this->getMethod() == 'GET'){
            return $_GET;
        }else{
            return $_POST;
        }
        
    }
}