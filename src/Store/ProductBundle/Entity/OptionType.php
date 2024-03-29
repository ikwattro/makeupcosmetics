<?php

namespace Store\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Store\ProductBundle\Repository\OptionTypeRepository")
 */
class OptionType
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
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Store\ProductBundle\Entity\OptionValue", mappedBy="option", cascade={"persist", "remove"})
     */
    protected $values;

    /**
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="options")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $products;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $is_image_option;

    /**
     * Set name
     *
     * @param string $name
     * @return Option
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->values = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add values
     *
     * @param \Store\ProductBundle\Entity\OptionValue $values
     * @return OptionType
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

    /**
     * Set is_image_option
     *
     * @param boolean $isImageOption
     * @return OptionType
     */
    public function setIsImageOption($isImageOption)
    {
        $this->is_image_option = $isImageOption;
    
        return $this;
    }

    /**
     * Get is_image_option
     *
     * @return boolean 
     */
    public function getIsImageOption()
    {
        return $this->is_image_option;
    }
}