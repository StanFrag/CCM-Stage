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
}