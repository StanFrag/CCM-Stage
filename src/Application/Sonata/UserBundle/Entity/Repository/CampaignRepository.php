<?php

namespace Application\Sonata\UserBundle\Entity\Repository;

use Application\Sonata\UserBundle\Entity\Base;
use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * CampaignRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CampaignRepository extends EntityRepository
{
    public function findActiveCampaign(){
        $qb = $this->createQueryBuilder('c');

        $t = $qb->select('c.id')
            ->where('c.state = :etat')
            ->setParameter('etat', true);

        $query = $t->getQuery();

        return $query->execute(); // instanceof Doctrine\ODM\MongoDB\EagerCursor
    }

    public function countActiveCampaign(){
        $qb = $this->createQueryBuilder('c');

        $t = $qb->select('COUNT(c)')
            ->where('c.state = :etat')
            ->setParameter('etat', true);

        $query = $t->getQuery();

        return $query->getSingleScalarResult(); // instanceof Doctrine\ODM\MongoDB\EagerCursor
    }

    public function findCampaignByBase(Base $base){
        $qb = $this->createQueryBuilder('c');

        $t = $qb->select('c')
            ->where('c.base = :base')
            ->setParameter('base', $base);

        $query = $t->getQuery();

        return $query->execute(); // instanceof Doctrine\ODM\MongoDB\EagerCursor
    }

    public function findCampaignWithMatching(){
        $qb = $this->createQueryBuilder('c');

        $t = $qb->select('c')
            ->where('c.state = :etat')
            ->setParameter('etat', true)
            ->orderBy('c.createdAt','DESC');

        $query = $t->getQuery();

        return $query->execute(); // instanceof Doctrine\ODM\MongoDB\EagerCursor
    }
}
