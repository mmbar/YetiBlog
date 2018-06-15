<?php


namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testResgier()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rejestracja');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertCount(5, $crawler->filter('input'));
    }
}