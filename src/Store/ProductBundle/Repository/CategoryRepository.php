<?php

namespace Store\ProductBundle\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CategoryRepository extends NestedTreeRepository
{
    public function findByLocale($id, $locale = 'fr')
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



    public function findAllByLocale($locale = 'fr', $onlyTops = false, $arrayze = false)
    {
        //Make a Select query
        $qb = $this->createQueryBuilder('a');
        $qb->select('a');
        if ($onlyTops == true) {
            $qb->where('a.parent is NULL');
        }

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

        if (true === $arrayze) {
            return $query->getArrayResult();
        }
        return $query->getResult();
    }

    public function findAllByLocaleWithChildren($locale = 'fr', $onlyTops = false, $arrayze = false)
    {
        //Make a Select query
        $qb = $this->createQueryBuilder('a');
        $qb->select('a');
        $qb->addSelect('c')
            ->leftJoin('a.children', 'c');
        if ($onlyTops == true) {
            $qb->where('a.parent is NULL');
        }
        $qb->addOrderBy('a.position');

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

        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_INNER_JOIN, true);

        if (true === $arrayze) {
            return $query->getArrayResult();
        }
        return $query->getResult();
    }

    public function findAll()
    {
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
            'fr'
        );

        return $query->getResult();
    }
}