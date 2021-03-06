<?php

namespace AppBundle\Repository;

/**
 * AuthorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AuthorRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Retrieves authors by name based on a given search query
     *
     * @param string $query
     * @return \Doctrine\ORM\Query
     */
    public function searchName(string $query)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('AppBundle:Author', 'a')
            ->where('a.name LIKE ?1')
            ->setParameter('1', '%'.$query.'%')
            ->getQuery();

        return $qb;
    }
}
