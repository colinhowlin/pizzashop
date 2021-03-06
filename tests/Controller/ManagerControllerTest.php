<?php


use App\Controller\ManagerController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ManagerControllerTest extends WebTestCase
{

    public function testIndex(){
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'manager1@gmail.com',
            'PHP_AUTH_PW'   => 'password',
        ]);
        $crawler = $client->request('GET', '/manager');

        // Test for valid HTTP Response code - 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Test that contains text
        $this->assertContains(
            'Sales Reports',
            $client->getResponse()->getContent()
        );

        // Assert that there are the expected number of header tags.
        $this->assertCount(5, $crawler->filter('table'));

        // Assert that there are the expected number of anchor tags.
        $this->assertGreaterThanOrEqual(4, $crawler->filter('a')->count());
    }
}
