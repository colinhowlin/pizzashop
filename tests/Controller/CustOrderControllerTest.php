<?php


use App\Controller\CustOrderController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustOrderControllerTest extends WebTestCase
{

    public function testIndex(){
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'customer1@gmail.com',
            'PHP_AUTH_PW'   => 'password',
        ]);
        $crawler = $client->request('GET', '/menu');

        // Test for valid HTTP Response code - 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Test that contains text
        $this->assertContains(
            'Extra Toppings',
            $client->getResponse()->getContent()
        );

        // Assert that there are the expected number of header tags.
        $this->assertGreaterThan(4, $crawler->filter('table')->count());

        // Assert that there are the expected number of anchor tags.
        $this->assertCount(2, $crawler->filter('button'));
    }
}
