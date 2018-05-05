<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 01/04/2018
 * Time: 00:35
 */

namespace AppBundle\Command\Api;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OAuthAddClientCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('oauth:add-client')
            ->setDescription('Adds a new client for OAuth');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $redirectUri = $this->getContainer()->getParameter('router.request_context.scheme');
        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris([$redirectUri]);
        $client->setAllowedGrantTypes(['refresh_token', 'password']);
        $clientManager->updateClient($client);
    }
}