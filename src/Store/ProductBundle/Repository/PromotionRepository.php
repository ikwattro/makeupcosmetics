<?php

namespace Store\ProductBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PromotionRepository extends EntityRepository
{
    public function findIfActual()
    {
        $now = new \DateTime("NOW");

        $q = $this->createQueryBuilder('p')
            ->where('p.start < :now')
            ->andWhere('p.end is NULL')
            ->orWhere('p.end > :now')
            ->andWhere('p.disabled = 0')
            ->andWhere('p.archived is NULL')
            ->orderBy('p.start')
            ->setParameter('now', $now);

        $query = $q->getQuery();

        return $query->getResult();

    }
}