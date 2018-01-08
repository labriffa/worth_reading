<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 08/01/2018
 * Time: 00:24
 */

namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;

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
        yield ['/posts'];
        yield ['/post/fixture-post-1'];
        yield ['/blog/category/fixture-category'];
        yield ['/archives'];
    }
}