<?php

namespace Store\ProductBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CartRepository extends EntityRepository
{
    public function findAll()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->addSelect('i')
            ->leftJoin('c.items', 'i')
            ->orderBy('c.cart_dtg', 'DESC');

        $q = $qb->getQuery();

        return $q->getResult();

    }
}