<?php

namespace Core;

class RedirectResponse extends BaseResponse
{
    public function __construct(
        private string $targetUrl,
        int $statusCode = 302
    ) {
        parent::__construct($statusCode);
        $this->withHeader('Location', $targetUrl);
    }

    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    public function getBody(): string
    {
        return '';
    }
}
