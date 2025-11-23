<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testIndexRoute_thenReturnHtmlAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'text/html; charset=UTF-8');
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Ecoscraping');
        $this->assertSelectorTextContains('p', 'Ceci est une page HTML statique');
        $this->assertSelectorTextContains('strong', 'MainController');
        $this->assertSelectorExists('.container');
        $this->assertSelectorExists('html[lang="fr"]');
        $this->assertSelectorExists('head meta[charset="UTF-8"]');
        $this->assertSelectorExists('title');
        $this->assertSelectorExists('body');
        $this->assertSelectorTextContains('title', 'Page Principale');
    }
}
