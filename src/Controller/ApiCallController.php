<?php

namespace App\Controller;

use App\Application\UseCase\FetchH1Headings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
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
