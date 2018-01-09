<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 07/01/2018
 * Time: 23:49
 */

namespace AppBundle\Service;


use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * An abstract class responsible for representing a generic entity service
 * providing core functionality
 *
 * Class EntityService
 * @package AppBundle\Service
 */
class EntityService
{
    private $repo;
    private $em;
    private $pagination;

    const DEFAULT_PAGE_PARAM_KEY = 'page';

    public function __construct(ObjectRepository $repo, EntityManagerInterface $em, PaginationService $pageService)
    {
        $this->repo = $repo;
        $this->em = $em;
        $this->pagination = $pageService;
    }

    public function getRepo() : ObjectRepository
    {
        return $this->repo;
    }

    public function getEm() : EntityManagerInterface
    {
        return $this->em;
    }

    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * Adds pagination functionality to a given collection
     *
     * @param $data
     * @param string $pageParamKey
     * @return mixed
     */
    public function paginate($data, $pageParamKey=EntityService::DEFAULT_PAGE_PARAM_KEY)
    {
        return $this->pagination->paginate($data, $pageParamKey);
    }
}