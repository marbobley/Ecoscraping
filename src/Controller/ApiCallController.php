<?php

namespace App\Controller;

use App\Application\UseCase\FetchH1Headings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class ApiCallController extends AbstractController
{
    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     */
    #[Route('/api/call', name: 'app_api_call', methods: ['GET'])]
    public function callLocalApi(Request $request, FetchH1Headings $fetchH1): JsonResponse
    {
        // Récupérer l'URL depuis les paramètres de requête
        $url = $request->query->get('url', 'http://localhost:8000/');

        // Validation de l'URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->json([
                'error' => 'Invalid URL format',
            ], 400);
        }

        // Protection SSRF : bloquer les IPs privées et localhost
        $host = parse_url($url, PHP_URL_HOST);
        if ($host && (filter_var($host, FILTER_VALIDATE_IP) && !filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))) {
            return $this->json([
                'error' => 'Access to private IP addresses is not allowed',
            ], 403);
        }

        // Protection contre localhost
        if (in_array(strtolower($host), ['localhost', '127.0.0.1', '::1'])) {
            return $this->json([
                'error' => 'Access to localhost is not allowed',
            ], 403);
        }

        try {
            $h1 = $fetchH1($url);
            return $this->json([
                'url' => $url,
                'h1' => $h1,
                'count' => count($h1),
            ]);
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            return $this->json([
                'error' => 'Failed to fetch the URL',
                'message' => $e->getMessage(),
            ], 502);
        }
    }
}
