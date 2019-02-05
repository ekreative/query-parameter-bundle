<?php

declare(strict_types=1);

namespace Mcfedr\UuidExtraBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestControllerTest extends WebTestCase
{
    public function testBoolTrue()
    {
        $client = self::createClient();

        $client->request('GET', '/param?test=1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('true', $client->getResponse()->getContent());
    }

    public function testBoolFalse()
    {
        $client = self::createClient();

        $client->request('GET', '/param?test=0');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('false', $client->getResponse()->getContent());
    }

    public function testBoolRequired()
    {
        $client = self::createClient();

        $client->request('GET', '/param');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testModel()
    {
        $client = self::createClient();

        $client->request('GET', '/model?name=fred');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('fred', $client->getResponse()->getContent());
    }
}
