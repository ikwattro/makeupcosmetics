<?php

namespace Store\MarketingBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FollowBackRepository extends EntityRepository
{
    public function findAllDesc()
    {
        $qb = $this->createQueryBuilder('f')
            ->orderBy('f.id', 'DESC');

        $q = $qb->getQuery();

        return $q->getResult();
    }
}