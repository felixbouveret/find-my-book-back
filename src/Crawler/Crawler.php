<?php

namespace App\Crawler;

use Symfony\Component\Panther\Client;

class Crawler
{
    protected $url = "";

    function __construct($url)
    {
        $this->url = $url;
        $this->client = Client::createChromeClient();
        $this->client->request('GET', $url);
    }

    public function getResult()
    {
        $this->client->clickLink('Tous les livres');
        $crawler = $this->client->waitFor('#installing-the-framework');
        return $crawler->filter('#s-refinements')->text();
    }
}