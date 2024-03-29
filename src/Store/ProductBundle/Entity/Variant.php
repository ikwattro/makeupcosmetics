<?php

namespace Store\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Variant
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Store\ProductBundle\Entity\Product", inversedBy="variants")
     */
    protected $product;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $is_master;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $sku;

    /**
     * @ORM\Column(type="float")
     */
    protected $price;

    /**
     * @ORM\ManyToMany(targetEntity="Store\ProductBundle\Entity\OptionValue", inversedBy="variants", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="value_variant")
     */
    protected $values;

    /**
     * @ORM\OneToMany(targetEntity="Store\ProductBundle\Entity\VariantImage", mappedBy="variant")
     */
    protected $images;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $out_of_stock;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isPromo;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $promoPrice;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $vslug;

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
     * Set is_master
     *
     * @param boolean $isMaster
     * @return Variant
     */
    public function setIsMaster($isMaster)
    {
        $this->is_master = $isMaster;
    
        return $this;
    }

    /**
     * Get is_master
     *
     * @return boolean 
     */
    public function getIsMaster()
    {
        return $this->is_master;
    }

    /**
     * Set sku
     *
     * @param string $sku
     * @return Variant
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
     * Set price
     *
     * @param integer $price
     * @return Variant
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set product
     *
     * @param \Store\ProductBundle\Entity\Product $product
     * @return Variant
     */
    public function setProduct(\Store\ProductBundle\Entity\Product $product = null)
    {
        $this->product = $product;
    
        return $this;
    }

    /**
     * Get product
     *
     * @return \Store\ProductBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->values = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getImages()
    {
        return $this->images;
    }
    
    /**
     * Add values
     *
     * @param \Store\ProductBundle\Entity\OptionValue $values
     * @return Variant
     */
    public function addValue(\Store\ProductBundle\Entity\OptionValue $values)
    {
        $this->values[] = $values;
    
        return $this;
    }

    /**
     * Remove values
     *
     * @param \Store\ProductBundle\Entity\OptionValue $values
     */
    public function removeValue(\Store\ProductBundle\Entity\OptionValue $values)
    {
        $this->values->removeElement($values);
    }

    /**
     * Get values
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getValues()
    {
        return $this->values;
    }

    protected $vals;

    public function getVals()
    {
        return $this->vals;
    }

    public function setVals(\Doctrine\Common\Collections\ArrayCollection $vals)
    {
        $this->vals = $vals;
    }

    public function addVal($val, $c)
    {
        $this->vals[$c] = $val;
    }

    public function getValFor($i)
    {
        return $this->vals[$i];
    }

    public function __toString()
    {
        $values = $this->getValues();
        $str = '';
        $options = array();
        foreach ($values as $val) {
            $str .= $val->getOption()->getName().' : '.$val->getName().' - ';
        }
        return $str;

    }

    public function getOutOfStock()
    {
        return $this->out_of_stock;
    }

    public function setOutOfStock($OutOfStock)
    {
        $this->out_of_stock = $OutOfStock;

        return $this;
    }

    /**
     * Set isPromo
     *
     * @param boolean $isPromo
     * @return Variant
     */
    public function setIsPromo($isPromo)
    {
        $this->isPromo = $isPromo;

        return $this;
    }

    /**
     * Get isPromo
     *
     * @return boolean 
     */
    public function getIsPromo()
    {
        return $this->isPromo;
    }

    /**
     * Set promoPrice
     *
     * @param float $promoPrice
     * @return Variant
     */
    public function setPromoPrice($promoPrice)
    {
        $this->promoPrice = $promoPrice;

        return $this;
    }

    /**
     * Get promoPrice
     *
     * @return float 
     */
    public function getPromoPrice()
    {
        return $this->promoPrice;
    }

    /**
     * Add images
     *
     * @param \Store\ProductBundle\Entity\VariantImage $images
     * @return Variant
     */
    public function addImage(\Store\ProductBundle\Entity\VariantImage $images)
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \Store\ProductBundle\Entity\VariantImage $images
     */
    public function removeImage(\Store\ProductBundle\Entity\VariantImage $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Set vslug
     *
     * @param string $vslug
     * @return Variant
     */
    public function setVslug($vslug)
    {
        $this->vslug = $vslug;

        return $this;
    }

    /**
     * Get vslug
     *
     * @return string 
     */
    public function getVslug()
    {
        if(empty($this->vslug)) {
            return $this->getProduct()->getSlug();
        } else {
            return $this->getProduct()->getSlug().'-'.$this->vslug;
        }
    }

    public function getName()
    {
        $title = '';
        foreach ($this->getValues() as $val) {
            $title = $title.' '.$val->getName();
        }
        return $title;
    }

    public function getFullName()
    {
        $title = $this->getProduct()->getName();
        foreach ($this->getValues() as $val) {
            $title = $title.' '.$val->getName();
        }
        return $title;
    }
}
