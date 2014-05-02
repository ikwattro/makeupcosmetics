<?php
namespace Store\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * @ORM\Table(name="category_translations", indexes={
 *      @ORM\Index(name="category_translation_idx", columns={"locale", "object_class", "field", "foreign_key"})
 * })
 * @ORM\Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\TranslationRepository")
 */
class CategoryEntityTranslation extends AbstractTranslation
{
    /**
     * All required columns are mapped through inherited superclass
     */
}