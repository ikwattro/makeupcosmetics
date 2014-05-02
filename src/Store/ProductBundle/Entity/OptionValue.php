<?php

namespace Store\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OptionValue
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
     * @ORM\ManyToOne(targetEntity="Store\ProductBundle\Entity\OptionType", inversedBy="values")
     */
    protected $option;

    /**
     * @ORM\ManyToMany(targetEntity="Variant", mappedBy="value")
     */
    protected $variants;

    public function __construct()
    {
        $this->variants = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return OptionValue
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
     * Set option
     *
     * @param \Store\ProductBundle\Entity\OptionType $option
     * @return OptionValue
     */
    public function setOption(\Store\ProductBundle\Entity\OptionType $option = null)
    {
        $this->option = $option;
    
        return $this;
    }

    /**
     * Get option
     *
     * @return \Store\ProductBundle\Entity\OptionType
     */
    public function getOption()
    {
        return $this->option;
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

    public function __toString()
    {
        return $this->name;
    }
}