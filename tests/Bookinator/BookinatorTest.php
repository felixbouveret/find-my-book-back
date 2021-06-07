<?php

namespace App\Tests\Bookinator;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookinatorTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $body = [
            "categorieChoosen" => [
                1, 2, 3
            ]
        ];
        $crawler = $client->xmlHttpRequest('POST', '/bookinator', $body);

        $this->assertResponseIsSuccessful();
    }
}