<?php

namespace Store\ProductBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OptionTypeRepository extends EntityRepository
{
    public function findWithValues()
    {
        $q = $this->createQueryBuilder('o');
        $q->addSelect('v')
            ->leftJoin('o.values', 'v');

        $query = $q->getQuery();

        return $query->getResult();
    }
}