<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 08/01/2018
 * Time: 00:24
 */

namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        yield ['/'];
        yield['/books/'];
        yield['/books/1'];
        yield['/books/search?q=/'];
        yield['/authors/'];
        yield['/authors/1'];
        yield['/genres/'];
        yield['/genres/1'];
    }
}