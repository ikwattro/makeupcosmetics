<?php

namespace Store\ProductBundle\Repository;

use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ProductRepository extends TranslationRepository
{
    public function findAllByLocale($locale = 'fr', $limit = null, $onlyAvailable = 1)
    {
        $strip = explode('_', $locale);
        $locale = $strip[0];
        //Make a Select query
        $qb = $this->createQueryBuilder('a');
        $qb->select('a');
        $qb->addSelect('c')
            ->addSelect('v')
            ->leftJoin('a.categories', 'c')
            ->leftJoin('a.variants', 'v');
        if ($onlyAvailable) {
            $qb->where('a.available = 1');
        }

        $qb->andWhere('v.is_master IS NOT null')
            ->orderBy('a.id');

        //->orderBy(...) customize it


        // Use Translation Walker
        $query = $qb->getQuery();

        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
        // Force the locale
        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale
        );
        return $query->getResult();
    }

    public function findAllByLocaleForTrans($locale = 'fr')
    {
        $strip = explode('_', $locale);
        $locale = $strip[0];
        //Make a Select query
        $qb = $this->createQueryBuilder('a');
        $qb->select('a');
        //->orderBy(...) customize it

        // Use Translation Walker
        $query = $qb->getQuery();

        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
        // Force the locale
        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale
        );
        return $query->getResult();
    }

    public function findByLocale($id, $locale = 'fr')
    {
        $q = $this->createQueryBuilder('p');
        $q->select('p')
            ->addSelect('c')
            ->addSelect('v')
            ->addSelect('x')
            ->addSelect('o')
          ->where('p.id = :id')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.variants', 'v')
            ->leftJoin('v.values', 'x')
            ->leftJoin('x.option', 'o')
          ->setParameter('id', $id)
            ->andWhere('v.is_master IS NOT null')
            ->orderBy('o.name', 'ASC');

        $query = $q->getQuery();
        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale
        );
        return $query->getSingleResult();
    }

    public function findAllByCategory($category, $locale = 'fr')
    {
        $strip = explode('_', $locale);
        $locale = $strip[0];
        //Make a Select query
        $qb = $this->createQueryBuilder('a');
        $qb->select('a');
        $qb->addSelect('c')
            ->addSelect('v')
            ->leftJoin('a.categories', 'c')
            ->leftJoin('a.variants', 'v')
            ->orderBy('a.id')
            ->where('v.is_master IS NOT null')
            ->andWhere('c.id = :cat')
            ->setParameter('cat', $category);
        //->orderBy(...) customize it

        // Use Translation Walker
        $query = $qb->getQuery();

        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
        // Force the locale
        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale
        );
        return $query->getResult();
    }

    public function findSimpleByLocale($id, $locale = 'fr')
    {
        $q = $this->createQueryBuilder('p');
        $q->select('p')
            ->where('p.id = :id')
            ->setParameter('id', $id);

        $query = $q->getQuery();
        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $query->setHint(
            \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $locale
        );
        return $query->getSingleResult();
    }
}