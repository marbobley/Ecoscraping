<?php

namespace App\Infrastructure\Http;

use App\Domain\Http\HttpClientPort;
use App\Domain\Http\HttpResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SymfonyHttpClientAdapter implements HttpClientPort
{
    public function __construct(private readonly HttpClientInterface $client)
    {
    }

    public function get(string $url): HttpResponse
    {
        $response = $this->client->request('GET', $url);

        $statusCode = $response->getStatusCode();
        $headers = $response->getHeaders(false);
        $contentType = $headers['content-type'][0] ?? 'text/plain; charset=UTF-8';
        $body = $response->getContent();

        return new HttpResponse($statusCode, $contentType, $body);
    }
}
