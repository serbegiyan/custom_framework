<?php

namespace Core;

class HtmlResponse extends BaseResponse
{
    /**
     * @param int $statusCode
     */
    public function __construct(
        public string $body,
        int $statusCode = 200,
    ) {
        parent::__construct($statusCode);
        $this->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
