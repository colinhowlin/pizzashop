<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testIndex(){
        $client = static::createClient(); // Create client.
        $crawler = $client->request('GET', '/'); // Get default content.

        // Test for valid HTTP Response code - 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Test that contains text
        $this->assertContains(
            "to place an order.</p>\n",
            $client->getResponse()->getContent()
        );

        // Assert that there are the expected number of header tags.
        $this->assertCount(1, $crawler->filter('h2'));

        // Assert that there are the expected number of anchor tags.
        $this->assertGreaterThan(8, $crawler->filter('a')->count());
    }
}
