<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 06/01/2018
 * Time: 21:39
 */

namespace AppBundle\Service;

use AppBundle\Controller\BookController;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Controls the pagination logic for given collections
 *
 * Class PaginationService
 * @package AppBundle\Service
 */
class PaginationService
{
    private $paginator;
    private $requestStack;

    public function __construct($paginator, RequestStack $requestStack)
    {
        $this->paginator = $paginator;
        $this->requestStack = $requestStack;
    }

    /**
     * Adds pagination functionality to a given collection
     *
     * @param $arr
     * @param $pageParamKey
     * @return mixed
     */
    public function paginate($arr, $pageParamKey)
    {
        return $this->paginator->paginate(
            $arr,
            $this->requestStack->getCurrentRequest()->get($pageParamKey, 1),
            BookController::BOOKS_PER_PAGE,
            [
                'pageParameterName' => $pageParamKey
            ]
        );
    }
}