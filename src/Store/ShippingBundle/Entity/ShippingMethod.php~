<?php

namespace Store\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ShippingMethod
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $informations;

    /**
     * @ORM\ManyToOne(targetEntity="Store\ShippingBundle\Entity\Zone", inversedBy="shipping_methods")
     */
    protected $zone;

    /**
     * @ORM\ManyToOne(targetEntity="Store\ShippingBundle\Entity\Company")
     */
    protected $company;

    /**
     * @ORM\Column(type="float")
     */
    protected $price;

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
     * Set title
     *
     * @param string $title
     * @return ShippingMethod
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set informations
     *
     * @param string $informations
     * @return ShippingMethod
     */
    public function setInformations($informations)
    {
        $this->informations = $informations;
    
        return $this;
    }

    /**
     * Get informations
     *
     * @return string 
     */
    public function getInformations()
    {
        return $this->informations;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return ShippingMethod
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set zone
     *
     * @param \Store\ShippingBundle\Entity\Zone $zone
     * @return ShippingMethod
     */
    public function setZone(\Store\ShippingBundle\Entity\Zone $zone = null)
    {
        $this->zone = $zone;
    
        return $this;
    }

    /**
     * Get zone
     *
     * @return \Store\ShippingBundle\Entity\Zone 
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set company
     *
     * @param \Store\ShippingBundle\Entity\Company $company
     * @return ShippingMethod
     */
    public function setCompany(\Store\ShippingBundle\Entity\Company $company = null)
    {
        $this->company = $company;
    
        return $this;
    }

    /**
     * Get company
     *
     * @return \Store\ShippingBundle\Entity\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }
}