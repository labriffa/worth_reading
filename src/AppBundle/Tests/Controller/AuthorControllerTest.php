<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthorControllerTest extends WebTestCase
{
    private $client;
    const ENDPOINT = '/authors';

    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreateNewAccessDeniedAnon()
    {
        $this->client->request('GET', AuthorControllerTest::ENDPOINT.'/new');

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreateNewAccessAcceptedUser()
    {
        $this->client->request('GET', AuthorControllerTest::ENDPOINT.'/new', [], [], [
            'PHP_AUTH_USER' => 'dan',
            'PHP_AUTH_PW' => '123'
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
