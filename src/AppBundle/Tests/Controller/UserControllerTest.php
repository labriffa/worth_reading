<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * @runInSeparateProcess
     */
    public function testWishlistAccessDeniedAnon()
    {
        $this->client->request('GET', '/wishlist');

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @runInSeparateProcess
     */
    public function testWishlistAccessAcceptedUser()
    {
        $this->client->request('GET', '/wishlist', [], [], [
            'PHP_AUTH_USER' => 'dan',
            'PHP_AUTH_PW' => '123'
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @runInSeparateProcess
     */
    public function testMyBooksAccessDeniedAnon()
    {
        $this->client->request('GET', '/my-books');

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @runInSeparateProcess
     */
    public function testMyBooksAccessAcceptedUser()
    {
        $this->client->request('GET', '/my-books', [], [], [
            'PHP_AUTH_USER' => 'dan',
            'PHP_AUTH_PW' => '123'
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
