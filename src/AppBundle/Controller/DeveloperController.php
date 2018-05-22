<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 19/05/2018
 * Time: 01:36
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Api\Client;
use AppBundle\Form\Api\ClientType;
use AppBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DeveloperController extends Controller
{
    /**
     * Controls the index page for the developer panel
     *
     * @Template(":developer:index.html.twig")
     * @param UserService $userService
     * @return mixed
     */
    public function indexAction(Request $request)
    {
//        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
//        $client = $clientManager->createClient();
//        $client->setRedirectUris(array('http://localhost:8000'));
//        $client->setAllowedGrantTypes(array('token', 'client_credentials'));
//        $client->setUser($this->getUser());
//        $clientManager->updateClient($client);


        $clients = $this->getUser()->getClients()->toArray();

        dump($clients);

        $form = $this->createForm(ClientType::class, new Client());

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
            $client = $clientManager->createClient();
            $client->setAllowedGrantTypes(array('token', 'client_credentials'));
            $client->setApplicationName($form->get('applicationName')->getData());
            $client->setUser($this->getUser());
            $clientManager->updateClient($client);
            return $this->redirect($request->getUri());
        }

        return [
            "clients" => $clients,
            "form" => $form->createView()
        ];
    }

    public function createAction(Request $request) {

        $form = $this->createForm(ClientType::class, new Client());

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
            $client = $clientManager->createClient();
            $client->setAllowedGrantTypes(array('token', 'client_credentials'));
            $client->setApplicationName($form->get('applicationName')->getData());
            $client->setUser($this->getUser());
            $clientManager->updateClient($client);
        }

        return ['form' => $form->createView()];
    }
}