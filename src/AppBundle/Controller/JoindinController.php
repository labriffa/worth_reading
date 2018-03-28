<?php


namespace AppBundle\Controller;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class JoindinController extends Controller
{
    public function eventsAction()
    {
        $client = new Client();
        $res = $client->request('GET', 'https://api.joind.in/v2.1/events', [
            'query' => ['filter'=>'past']
        ]);
        $res_arr = json_decode($res->getBody(), true);
        //return new Response(json_encode(json_decode($res->getBody())));
        return $this->render(':joindin:index.html.twig', [
            'events' => $res_arr['events']
        ]);
    }

    public function eventAction($title)
    {
        $client = new Client();
        $res = $client->request('GET', 'https://api.joind.in/v2.1/events', [
            'query' => ['title' => urldecode($title)]
        ]);

        $res_arr = json_decode($res->getBody(), true);
        //return new Response(json_encode($res->getStatusCode()));
        return $this->render(':joindin:event.html.twig', [
            'event' => $res_arr['events'][0]
        ]);
    }

    public function talksAction($title)
    {
        $client = new Client();

        $res = $client->request('GET', 'https://api.joind.in/v2.1/events', [
            'query' => ['title' => urldecode($title)]
        ]);

        $res_arr = json_decode($res->getBody(), true);
        $event_uri = $res_arr['events'][0]['talks_uri'];

        $res = $client->request('GET', $event_uri.'/talks');

        dump($res);

        $res_arr = json_decode($res->getBody(), true);

        dump($res_arr);

        //return new Response(json_encode($res->getStatusCode()));
        return $this->render(':joindin:talks.html.twig', [
            'talks' => $res_arr['talks']
        ]);
    }
}