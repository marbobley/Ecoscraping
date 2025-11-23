<?php

namespace App\Domain\Http;

interface HttpClientPort
{
    /**
     * Perform a GET request to the given URL and return a simplified response.
     */
    public function get(string $url): HttpResponse;
}
