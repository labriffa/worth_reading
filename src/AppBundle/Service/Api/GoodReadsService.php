<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 18/05/2018
 * Time: 04:15
 */

namespace AppBundle\Service\Api;

use GuzzleHttp\Client;

class GoodReadsService
{
    private $client;
    const BASE_URL = 'https://www.goodreads.com';
    const API_KEY = 'Y2L2h66TxHsrUF3sAH71dA';

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Retrieves GoodRead reviews for a given book that corresponds to the given ISBN
     *
     * @param string $isbn
     * @return array
     */
    public function getReviews(string $isbn)
    {
        $good_reads_reviews = [];

        $response = $this->client->get(GoodReadsService::BASE_URL . '/book/isbn/'. $isbn,
            [
                'query' =>
                    [
                        'key' => GoodReadsService::API_KEY,
                        'format' => 'xml'
                    ],
                'http_errors' => false
            ]
        );

        $xml_response = $response->getBody();

        // convert xml response to an object
        $xml = simplexml_load_string($xml_response, "SimpleXMLElement", LIBXML_NOCDATA);

        // encode the object so that we can convert it into an array
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        // retrieve the book reviews
        if(isset($array['book']['reviews_widget'])) {
            $good_reads_reviews = $array['book']['reviews_widget'];
        }

        return $good_reads_reviews;
    }

    public function search(string $term) {

        $response = $this->client->get(GoodReadsService::BASE_URL . '/search/index.xml',
            [
                'query' =>
                    [
                        'q' => $term,
                        'key' => GoodReadsService::API_KEY,
                        'format' => 'xml'
                    ],
                'http_errors' => false
            ]
        );

        $xmlstring = $response->getBody();

        $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $results = json_decode($json,TRUE);

        return $results;
    }
}