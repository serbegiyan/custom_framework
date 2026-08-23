<?php

namespace Core;

use App\Interfaces\ResponseInterface;

class ResponseEmitter
{
    public function emit(ResponseInterface $response): void
    {
        $check = headers_sent();
        if (!$check) {
            http_response_code($response->getStatusCode());
            $headers = $response->getHeaders();
            foreach ($headers as $key => $value) {
                $header = $key . ': ' . $value;
                header($header);
            }
            $cookies = $response->getCookies();
            foreach ($cookies as $name => $cookieData) {
                $options = $cookieData['options'];
                $options['value'] = $cookieData['value'];

                setcookie($name, $options);
            }
        }
        echo $response->getBody();
    }
}
