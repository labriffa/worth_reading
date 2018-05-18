<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 10/05/2018
 * Time: 00:01
 */

use AppBundle\Controller\Api\BookApiController;
use PHPUnit\Framework\TestCase;

class BookApiControllerTest extends TestCase
{
    public function testGet() {

        $client = new \GuzzleHttp\Client([
           'base_url' => 'http://localhost:8003',
           'defaults' => [
               'exceptions' => false,
           ]
        ]);


    }
}
