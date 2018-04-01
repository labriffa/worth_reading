<?php
/**
 * Created by PhpStorm.
 * User: lewisbriffa
 * Date: 25/12/2017
 * Time: 02:12
 */

namespace AppBundle\Service;

use AppBundle\Entity\Author;
use AppBundle\Repository\AuthorRepository;
use Doctrine\ORM\EntityManager;

/**
 * A class that exposes a number of functionalities concerned with
 * author entities
 *
 * Class AuthorService
 * @package AppBundle\Service
 */
class AuthorService extends EntityService
{
    public function __construct(AuthorRepository $repo, EntityManager $em, PaginationService $pageService)
    {
        parent::__construct($repo, $em, $pageService);
    }

    public function getAll() {
        $this->getRepo()->findAll();
    }

    /**
     * Adds a given author
     *
     * @param Author $author
     */
    public function add(Author $author)
    {
        $this->getRepo()->persist($author);
        $this->getEm()->flush();
    }

    /**
     * Retrieves authors by their name based on a given
     * search term
     *
     * @param string $query
     * @return \Doctrine\ORM\Query
     */
    public function searchName(string $query)
    {
        return $this->getRepo()->searchName($query);
    }
}