<?php

namespace Store\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Store\ProductBundle\Repository\ProductRepository")
 * @Gedmo\TranslationEntity(class="Store\ProductBundle\Entity\ProductEntityTranslation")
 * @ORM\HasLifecycleCallbacks
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Gedmo\Translatable()
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $available;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $sku;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Translatable
     */
    protected $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Translatable
     */
    protected $meta_description;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Translatable
     */
    protected $meta_keywords;

    /**
     * @Gedmo\Locale
     */
    protected $locale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

    /**
     * @ORM\ManyToMany(targetEntity="Store\ProductBundle\Entity\Category", cascade={"persist", "remove"})
     */
    protected $categories;

    /**
     * @ORM\OneToMany(targetEntity="Store\ProductBundle\Entity\Variant", mappedBy="product")
     */
    protected $variants;

    /**
     * @ORM\ManyToMany(targetEntity="Store\ProductBundle\Entity\OptionType", cascade={"persist", "remove"}, inversedBy="products")
     * @ORM\JoinTable(name="product_optiontype",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="optiontype_id", referencedColumnName="id", nullable=true)})
     */
    protected $options;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $deleted;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fileupdate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $twoPlusOne;


    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->options = new \Doctrine\Common\Collections\ArrayCollection();
        $this->variants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set product as deleted
     */
    public function setDeleted()
    {
        $this->deleted = true;
    }

    /**
     * Return if the product is deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        if (true === $this->deleted) {
            return true;
        }
        return false;
    }

    /**
     * Add options
     */
    public function addOption(\Store\ProductBundle\Entity\OptionType $option)
    {
        $this->options[] = $option;
    }

    public function removeOption(\Store\ProductBundle\Entity\OptionType $option)
    {
        $this->options->removeElement($option);
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Add variants
     */
    public function addVariant(\Store\ProductBundle\Entity\Variant $variant)
    {
        $this->variants[] = $variant;
    }

    public function removeVariant(\Store\ProductBundle\Entity\Variant $variant)
    {
        $this->variants->removeElement($variant);
    }

    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * Add categories
     *
     */
    public function addCategory(\Store\ProductBundle\Entity\Category $category) // addCategorie sans « s » !
    {
        // Ici, on utilise l'ArrayCollection vraiment comme un tableau, avec la syntaxe []
        $this->categories[] = $category;
    }

    /**
     * Remove categories
     *
     */
    public function removeCategory(\Store\ProductBundle\Entity\Category $category) // removeCategorie sans « s » !
    {
        // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCategories() // Notez le « s », on récupère une liste de catégories ici !
    {
        return $this->categories;
    }

    /**
     * @Gedmo\Translatable
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=128)
     */
    private $slug;

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'uploads/documents';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            // faites ce que vous voulez pour générer un nom unique
            $this->path = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // s'il y a une erreur lors du déplacement du fichier, une exception
        // va automatiquement être lancée par la méthode move(). Cela va empêcher
        // proprement l'entité d'être persistée dans la base de données si
        // erreur il y a
        $this->file->move($this->getUploadRootDir(), $this->path);

        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sku
     *
     * @param string $sku
     * @return Product
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    
        return $this;
    }

    /**
     * Get sku
     *
     * @return string 
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set available
     *
     * @param boolean $available
     * @return Product
     */
    public function setAvailable($available)
    {
        $this->available = $available;
    
        return $this;
    }

    /**
     * Get available
     *
     * @return boolean 
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * Set meta_description
     *
     * @param string $metaDescription
     * @return Product
     */
    public function setMetaDescription($metaDescription)
    {
        $this->meta_description = $metaDescription;
    
        return $this;
    }

    /**
     * Get meta_description
     *
     * @return string 
     */
    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    /**
     * Set meta_keywords
     *
     * @param string $metaKeywords
     * @return Product
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->meta_keywords = $metaKeywords;
    
        return $this;
    }

    /**
     * Get meta_keywords
     *
     * @return string 
     */
    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Product
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Product
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getMasterVariant()
    {
        $variants = $this->getVariants();
        foreach ($variants as $v) {
            if (true === $v->getIsMaster()) {
                return $v;
            }
        }
    }

    public function getPrice()
    {
        return $this->getMasterVariant()->getPrice();
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Set fileupdate
     *
     * @param string $fileupdate
     * @return Product
     */
    public function setFileupdate($fileupdate)
    {
        $this->fileupdate = $fileupdate;

        return $this;
    }

    /**
     * Get fileupdate
     *
     * @return string 
     */
    public function getFileupdate()
    {
        return $this->fileupdate;
    }

    /**
     * Set twoPlusOne
     *
     * @param boolean $twoPlusOne
     * @return Product
     */
    public function setTwoPlusOne($twoPlusOne)
    {
        $this->twoPlusOne = $twoPlusOne;

        return $this;
    }

    /**
     * Get twoPlusOne
     *
     * @return boolean 
     */
    public function getTwoPlusOne()
    {
        return $this->twoPlusOne;
    }
}
