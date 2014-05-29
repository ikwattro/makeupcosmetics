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

    public function findAllForUser($userId)
    {
        $q = $this->createQueryBuilder('c')
            ->where('c.customer = :user')
            ->andWhere('c.state = :state')
            ->setParameter('user', $userId)
            ->setParameter('state', 'PAYMENT_COMPLETE');

        $qu = $q->getQuery();

        return $qu->getResult();
    }

    public function findAllForAdmin()
    {
        $qb = $this->createQueryBuilder('c');
            $qb->orderBy('c.cart_dtg', 'DESC')
                ->where('c.items_total > 0');




        $q = $qb->getQuery();

        return $q->getResult();

    }
}