<?php

declare(strict_types=1);

namespace Mcfedr\UuidExtraBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestControllerTest extends WebTestCase
{
    public function testBoolTrue()
    {
        $client = self::createClient();

        $client->request('GET', '/param/bool?test=1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('true', $client->getResponse()->getContent());
    }

    public function testBoolFalse()
    {
        $client = self::createClient();

        $client->request('GET', '/param/bool?test=0');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('false', $client->getResponse()->getContent());
    }

    public function testInt()
    {
        $client = self::createClient();

        $client->request('GET', '/param/int?test=123');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('123', $client->getResponse()->getContent());
    }

    public function testString()
    {
        $client = self::createClient();

        $client->request('GET', '/param/string?test=hello');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('hello', $client->getResponse()->getContent());
    }

    public function testDate()
    {
        $client = self::createClient();

        $client->request('GET', '/param/date?test='.urlencode('2018-11-12T00:00:00Z'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('2018-11-12T00:00:00+00:00', $client->getResponse()->getContent());
    }

    public function testFloat()
    {
        $client = self::createClient();

        $client->request('GET', '/param/float?test=12.34');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('12.34', $client->getResponse()->getContent());
    }

    public function testModel()
    {
        $client = self::createClient();

        $client->request('GET', '/model?name=fred');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('fred', $client->getResponse()->getContent());
    }
}
