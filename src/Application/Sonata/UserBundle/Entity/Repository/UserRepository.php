<?php

namespace Application\Sonata\UserBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    public function findNonAdmin(){

        $qb = $this->createQueryBuilder('u');

        $t = $qb->select('u');

        $query = $t->getQuery();

        return $query;
    }
}
