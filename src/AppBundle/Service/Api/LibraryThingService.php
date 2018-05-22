<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 18/05/2018
 * Time: 04:28
 */

namespace AppBundle\Service\Api;


use GuzzleHttp\Client;

class LibraryThingService
{
    private $client;
    const BASE_URL = 'http://www.librarything.com/api/thingISBN';

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Search for other editions of a given ISBN
     *
     * @param string $isbn
     * @return mixed
     */
    public function getOtherEditions(string $isbn)
    {
        $res = $this->client->get(LibraryThingService::BASE_URL . '/' . $isbn);

        $xml_response = $res->getBody();

        $xml = simplexml_load_string($xml_response, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        return $array;
    }
}