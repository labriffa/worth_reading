<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 08/01/2018
 * Time: 01:31
 */

namespace Tests;

use Symfony\Component\BrowserKit\Client as BaseClient;
use Symfony\Component\BrowserKit\Response;


class Client extends BaseClient
{

    /**
     * Makes a request.
     *
     * @param object $request An origin request instance
     *
     * @return object An origin response instance
     */
    protected function doRequest($request)
    {
        return $this->getResponse();
    }
}