<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 10/05/2018
 * Time: 01:41
 */

namespace AppBundle\Tests\Api;

use PHPUnit\Framework\TestCase;

class GenreApiControllerTest extends TestCase
{
    const BASE_URL = 'http://localhost:8003';
    private $access_token;
    private $genre_id;

    // check token creation
    public function testOAuth() {
        $client = new \GuzzleHttp\Client();

        $response = $client->post(BookApiControllerTest::BASE_URL .'/app_dev.php/oauth/v2/token', [
            'client_id' => '1_3bcbxd9e24g0gk4swg0kwgcwg4o8k8g4g888kwc44gcc0gwwk4',
            'client_secret' => '4ok2x70rlfokc8g0wws8c8kwcokw80k44sg48goc0ok4w0so0k',
            'username' => 'lewis',
            'password' => '123'
        ]);

        $response = json_decode($response->getBody());

        $this->assertArrayHasKey('access_token', $response);
        $this->access_token = $response["access_token"];
    }

    // check collection post
    public function testPost() {

        $client = new \GuzzleHttp\Client();

        // genre details
        $genre = [
            "name" => "Dime Novel",
        ];

        $response = $client->post(BookApiControllerTest::BASE_URL . '/api/v1/genres',  [
            'headers' => [
                'content-type' => 'application/json',
                'Authorization'     => 'Bearer ' . $this->access_token
            ],
            'body' => json_encode($genre),
        ]);


        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));

        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('id', $data);
        $this->genre_id = $data["id"];
    }

    //check collection gets
    public function testGet() {

        $client = new \GuzzleHttp\Client();

        // make request
        $response = $client->get(BookApiControllerTest::BASE_URL . '/api/v1/genres');

        // assert response
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('total', $data);

    }

    // check gets on individual items
    public function testGetSingle() {

        $client = new \GuzzleHttp\Client();

        // make request
        $response = $client->get(BookApiControllerTest::BASE_URL . '/api/v1/genres/'. $this->genre_id);

        $this->assertEquals(201, $response->getStatusCode());

        $response = json_decode($response->getBody(), true);

        $this->assertEquals($response["name"], 'Dime Novel');

    }

    // check deletes on individual items
    public function testDelete() {

        $client = new \GuzzleHttp\Client();

        // make request
        $response = $client->delete(BookApiControllerTest::BASE_URL . '/api/v1/genres/' . $this->genre_id);

        $this->assertEquals(201, $response->getStatusCode());
    }
}

