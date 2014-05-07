<?php

namespace Store\ProductBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Gedmo\Translatable\Entity\Repository\TranslationRepository;

class VariantRepository extends EntityRepository
{
    public function findById($id, $locale='fr')
    {
        $q = $this->createQueryBuilder('v');
        $q->select('v')
            ->addSelect('p')
            ->leftJoin('v.product', 'p')
            ->where('v.id = :id')
            ->setParameter('id', $id);

        $query = $q->getQuery();
        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale);
        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_INNER_JOIN, true);

        return $query->getOneOrNullResult();
    }
}