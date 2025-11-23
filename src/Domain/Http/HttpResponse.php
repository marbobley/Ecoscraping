<?php

namespace App\Domain\Http;

class HttpResponse
{
    public function __construct(
        public readonly int $statusCode,
        public readonly string $contentType,
        public readonly string $body,
    ) {}
}
