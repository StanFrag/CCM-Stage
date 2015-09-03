<?php

namespace Application\Sonata\UserBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;


class MatchingDetailRepository extends EntityRepository{

    public function findByIdMatching($idMatching){
        $qb = $this->createQueryBuilder('md');

        $t = $qb->select('md.md5')
            ->where('md.id_matching = :id')
            ->setParameter('id', $idMatching);

        $query = $t->getQuery();

        return $query->execute(); // instanceof Doctrine\ODM\MongoDB\EagerCursor
    }

    public function countMatchingDetailByUser(User $user){

        $null = null;
        $qb = $this->createQueryBuilder('md');

        $t = $qb->select('COUNT(md)')
            ->leftJoin('md.id_matching', 'm')
            ->leftJoin('m.base', 'b')
            ->where('b.user = :user')
            ->setParameter('user', $user);

        $query = $t->getQuery();

        return $query->getSingleScalarResult(); // instanceof Doctrine\ODM\MongoDB\EagerCursor
    }
}