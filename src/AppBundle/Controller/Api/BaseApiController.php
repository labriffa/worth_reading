<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 28/03/2018
 * Time: 19:14
 */

namespace AppBundle\Controller\Api;


use FOS\RestBundle\Controller\FOSRestController;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseApiController extends FOSRestController
{
    const DEFAULT_PAGE_LIMIT = 10;
    const DEFAULT_PAGE_NO = 1;

    protected function serialize($data, $format = 'json')
    {
        return $this->container->get('jms_serializer')
            ->serialize($data, $format);
    }

    protected function createApiResponse($data, $statusCode = 200)
    {
        $json = $this->serialize($data);

        return new Response($json, $statusCode, array(
            'Content-Type' => 'application/json'
        ));
    }

    /**
     * Handles pagination representations
     *
     * @param $request
     * @param $arr
     * @param $route
     * @return Response
     */
    protected function paginatedResponse($request, $arr, $route_name, $id=null)
    {

        // get query parameters
        $limit = $request->query->getInt('limit', BaseApiController::DEFAULT_PAGE_LIMIT);
        $page = $request->query->getInt('page', BaseApiController::DEFAULT_PAGE_NO);
        $sorting = $request->query->get('sorting', array());

        // create pager adapter
        $pagerAdapter = new ArrayAdapter($arr);
        $pager = new Pagerfanta($pagerAdapter);

        // set limits
        $pager->setCurrentPage($page);
        $pager->setMaxPerPage($limit);

        // create factory
        $pagerFactory = new PagerfantaFactory();

        // create route params array
        $route_params = ['page' => $page, 'limit' => $limit];

        // check for optional parameters
        if($id) {
            $route_params["id"] = $id;
        }

        // create and handle page representation
        $paginatedCollection = $pagerFactory->createRepresentation($pager, new Route($route_name, $route_params));

        return $this->handleView($this->view($paginatedCollection));
    }

    protected function convertBase64Image($path, $base64)
    {
        $filename = md5(uniqid());
        $fh = fopen( $path . '/' . $filename, "wb" );
        fwrite($fh, base64_decode($base64));
        fclose($fh);

        $file = new UploadedFile($path . '/' . $filename, $filename, "image/png", null, null, true);

        return $file;
    }
}