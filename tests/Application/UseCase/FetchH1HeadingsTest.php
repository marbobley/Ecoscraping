<?php

namespace App\Tests\Application\UseCase;

use App\Application\UseCase\FetchH1Headings;
use App\Domain\Http\HttpClientPort;
use App\Domain\Http\HttpResponse;
use PHPUnit\Framework\TestCase;

class FetchH1HeadingsTest extends TestCase
{
    private function makeUseCaseReturning(HttpResponse $response): FetchH1Headings
    {
        $http = $this->createMock(HttpClientPort::class);
        $http->method('get')->willReturn($response);

        return new FetchH1Headings($http);
    }

    public function testReturnsEmptyArrayWhenContentTypeIsNotHtml(): void
    {
        $response = new HttpResponse(200, 'application/json', '{"ok":true}');
        $useCase = $this->makeUseCaseReturning($response);

        $result = $useCase('http://example.com/api');

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    public function testExtractsSingleH1FromValidHtml(): void
    {
        $html = '<!doctype html><html lang="fr"><head><meta charset="utf-8"></head><body><h1>Hello World</h1></body></html>';
        $response = new HttpResponse(200, 'text/html; charset=UTF-8', $html);
        $useCase = $this->makeUseCaseReturning($response);

        $result = $useCase('http://example.com');

        $this->assertSame(['Hello World'], $result);
    }

    public function testExtractsMultipleH1AndTrimsWhitespace(): void
    {
        $html = '<h1>  First  </h1><div><h1>Second</h1></div><h1>   Third</h1>';
        $response = new HttpResponse(200, 'text/html', $html);
        $useCase = $this->makeUseCaseReturning($response);

        $result = $useCase('http://example.com');

        $this->assertSame(['First', 'Second', 'Third'], $result);
    }

    public function testIgnoresEmptyOrWhitespaceOnlyH1(): void
    {
        $html = '<h1>   </h1><h1>Not empty</h1><h1></h1>';
        $response = new HttpResponse(200, 'text/html', $html);
        $useCase = $this->makeUseCaseReturning($response);

        $result = $useCase('http://example.com');

        $this->assertSame(['Not empty'], $result);
    }
}
