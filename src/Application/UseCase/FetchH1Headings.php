<?php

namespace App\Application\UseCase;

use App\Domain\Http\HttpClientPort;

class FetchH1Headings
{
    public function __construct(private readonly HttpClientPort $http)
    {
    }

    /**
     * Fetch the given URL and return an array of H1 texts found in the HTML.
     * If the content is not HTML, returns an empty array.
     * Does not throw on HTML parsing errors; returns best-effort extraction.
     *
     * @return array<int,string>
     */
    public function __invoke(string $url): array
    {
        $response = $this->http->get($url);

        // Only attempt parsing if content-type indicates HTML
        if (stripos($response->contentType, 'text/html') === false && stripos($response->contentType, 'application/xhtml+xml') === false) {
            return [];
        }

        $h1Tags = [];
        $dom = new \DOMDocument();

        // Suppress libxml warnings for malformed HTML
        $prev = libxml_use_internal_errors(true);
        $dom->loadHTML($response->body, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $h1Elements = $dom->getElementsByTagName('h1');
        foreach ($h1Elements as $h1) {
            $text = trim($h1->textContent);
            if ($text !== '') {
                $h1Tags[] = $text;
            }
        }

        return $h1Tags;
    }
}
