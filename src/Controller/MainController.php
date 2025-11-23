<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $html = '
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Page Principale</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        max-width: 800px;
                        margin: 50px auto;
                        padding: 20px;
                        background-color: #f5f5f5;
                    }
                    .container {
                        background-color: white;
                        padding: 30px;
                        border-radius: 8px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    }
                    h1 {
                        color: #333;
                        border-bottom: 3px solid #007bff;
                        padding-bottom: 10px;
                    }
                    p {
                        color: #666;
                        line-height: 1.6;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Bienvenue sur Ecoscraping</h1>
                    <p>Ceci est une page HTML statique retournée directement par le contrôleur.</p>
                    <p>Le contrôleur <strong>MainController</strong> est accessible à la racine du site.</p>
                </div>
            </body>
            </html>
        ';

        return new Response($html);
    }


    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    #[Route('/api/call', name: 'app_api_call', methods: ['GET'])]
    public function callLocalApi(Request $request,HttpClientInterface $client): Response
    {
        // Récupérer l'URL depuis les paramètres de requête
        $url = $request->query->get('url', 'http://localhost:8000/');

        $response = $client->request(
            'GET',
            $url
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        // Extraire uniquement les balises h1
        $h1Tags = [];
        $dom = new \DOMDocument();

        // Désactiver temporairement les erreurs libxml
        libxml_use_internal_errors(true);
        $dom->loadHTML($content, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors(); // Nettoyer les erreurs capturées

        $h1Elements = $dom->getElementsByTagName('h1');
        foreach ($h1Elements as $h1) {
            $h1Tags[] = $h1->textContent;
        }

        dd($h1Tags);
        return new Response($content, $statusCode, ['Content-Type' => $contentType]);
    }
}
