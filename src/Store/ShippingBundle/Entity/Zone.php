<?php

namespace Store\ShippingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Zone
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
     * @ORM\Column(type="array")
     */
    protected $countries;

    /**
     * @ORM\OnetoMany(targetEntity="Store\ShippingBundle\Entity\ShippingMethod", mappedBy="zone")
     */
    protected $shipping_methods;

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
     * @return Zone
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
     * Set countries
     *
     * @param array $countries
     * @return Zone
     */
    public function setCountries($countries)
    {
        $this->countries = $countries;
    
        return $this;
    }

    /**
     * Get countries
     *
     * @return array 
     */
    public function getCountries()
    {
        return $this->countries;
    }

    public function __toString()
    {
        return $this->title;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shipping_methods = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add shipping_methods
     *
     * @param \Store\ShippingBundle\Entity\ShippingMethod $shippingMethods
     * @return Zone
     */
    public function addShippingMethod(\Store\ShippingBundle\Entity\ShippingMethod $shippingMethods)
    {
        $this->shipping_methods[] = $shippingMethods;
    
        return $this;
    }

    /**
     * Remove shipping_methods
     *
     * @param \Store\ShippingBundle\Entity\ShippingMethod $shippingMethods
     */
    public function removeShippingMethod(\Store\ShippingBundle\Entity\ShippingMethod $shippingMethods)
    {
        $this->shipping_methods->removeElement($shippingMethods);
    }

    /**
     * Get shipping_methods
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShippingMethods()
    {
        return $this->shipping_methods;
    }
}