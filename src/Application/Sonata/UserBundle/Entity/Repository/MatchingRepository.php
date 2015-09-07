<?php

namespace Application\Sonata\UserBundle\Entity\Repository;

use Application\Sonata\UserBundle\Entity\Base;
use Application\Sonata\UserBundle\Entity\Campaign;
use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;


class MatchingRepository extends EntityRepository{
    public function findByBase(Base $base){
        $qb = $this->createQueryBuilder('m');

        $t = $qb->select('m')
            ->where('m.base = :base')
            ->setParameter('base', $base);

        $query = $t->getQuery();

        return $query->execute(); // instanceof Doctrine\ODM\MongoDB\EagerCursor
    }

    public function findByCampaign(Campaign $campaign){
        $qb = $this->createQueryBuilder('m');

        $t = $qb->select('m')
            ->where('m.campaign = :campaign')
            ->setParameter('campaign', $campaign);

        $query = $t->getQuery();

        return $query->execute(); // instanceof Doctrine\ODM\MongoDB\EagerCursor
    }

    public function findAllFromCampaign(User $user, Campaign $campaign){
        $qb = $this->createQueryBuilder('m');

        $fields = array('b.title', 'm.date_maj', 'm.nb_match', 'm.id');

        $t = $qb->select($fields)
            ->leftJoin('m.base','b')
            ->where('b.user = :user')
            ->andWhere('m.campaign = :campaign')
            ->setParameter('user', $user)
            ->setParameter('campaign', $campaign)
            ->orderBy('b.modificated_at','DESC');

        $query = $t->getQuery();

        return $query->execute(); // instanceof Doctrine\ODM\MongoDB\EagerCursor
    }

    public function lastMatchingFromUser(User $user){

        $qb = $this->createQueryBuilder('m');

        $t = $qb->select('b.title', 'm.nb_match', 'c.title AS campaign', 'm.id')
            ->leftJoin('m.base', 'b')
            ->leftJoin('m.campaign', 'c')
            ->where('b.user = :user')
            ->setParameter('user', $user)
            ->orderBy('m.date_maj','DESC');

        $query = $t->getQuery()->setMaxResults(4);

        return $query->getResult(); // instanceof Doctrine\ODM\MongoDB\EagerCursor
    }
}