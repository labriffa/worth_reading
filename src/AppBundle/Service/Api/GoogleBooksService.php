<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 18/05/2018
 * Time: 04:03
 */

namespace AppBundle\Service\Api;

use GuzzleHttp\Client;

class GoogleBooksService
{
    private $client;
    const BASE_URL = 'https://www.googleapis.com/books/v1';

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Search for books based on a given ISBN
     *
     * @param string $isbn
     * @return mixed
     */
    public function searchISBN(string $isbn)
    {
        $response = $this->client->get(GoogleBooksService::BASE_URL . '/volumes',
            [
                'query' =>
                    [
                        'q' => 'isbn:' . $isbn
                    ]
            ]
        );

        $google_books = json_decode($response->getBody(), true);

        return $google_books;
    }
}