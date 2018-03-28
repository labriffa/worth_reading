<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AdminControllerTest extends WebTestCase
{
    private $client;
    const ENDPOINT = '/admin';


    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * @runInSeparateProcess
     */
    public function testAccessDeniedAnon()
    {
        $this->client->request('GET', AdminControllerTest::ENDPOINT);

        $this->client->followRedirect();

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @runInSeparateProcess
     */
    public function testAccessDeniedUser()
    {
        $this->client->request('GET', AdminControllerTest::ENDPOINT, [], [], [
            'PHP_AUTH_USER' => 'dan',
            'PHP_AUTH_PW' => '123'
        ]);

        $this->client->followRedirect();

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @runInSeparateProcess
     */
    public function testAccessAcceptedAdmin()
    {
        $this->client->request('GET', AdminControllerTest::ENDPOINT, [], [], [
            'PHP_AUTH_USER' => 'lewis',
            'PHP_AUTH_PW' => '123'
        ]);

        $this->client->followRedirect();

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
