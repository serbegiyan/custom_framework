<?php

namespace Core;

class JsonResponse extends BaseResponse
{
    /**
     * @param array<array-key, mixed> $data
     * @param int $statusCode
     */
    public function __construct(
        public array $data,
        int $statusCode = 200,
    ) {
        parent::__construct($statusCode);
        $this->withHeader('Content-Type', 'application/json; charset=utf-8');
    }

    public function getBody(): string
    {
        return json_encode($this->data, JSON_THROW_ON_ERROR);
    }
}
