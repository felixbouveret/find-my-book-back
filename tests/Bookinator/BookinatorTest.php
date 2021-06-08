<?php

namespace App\Tests\Bookinator;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class BookinatorTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $body = [
            "categorieChoosen" => [
                2, 3, 4
            ]
        ];
        $crawler = $client->xmlHttpRequest('POST', '/bookinator/firststep', $body);
        $jsonArray = json_decode($crawler->filter('html')->each(function (Crawler $node, $i) {
            return $node->text();
        })[0]);
        
        $randArray = array_rand($jsonArray, 3);
        $newBody =  ['bookChoosen' => []];

        foreach($randArray as $i) {
            array_push($newBody['bookChoosen'], $jsonArray[$i]->id);
        }

        $crawler = $client->xmlHttpRequest('POST', '/bookinator/secondstep', $newBody);
        $newJsonArray = json_decode($crawler->filter('html')->each(function (Crawler $node, $i) {
            return $node->text();
        })[0]);

        $this->assertResponseIsSuccessful();
    }
}