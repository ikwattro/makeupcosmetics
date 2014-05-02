<?php

namespace Store\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CartItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Store\ProductBundle\Entity\Variant")
     */
    protected $product;

    /**
     * @ORM\Column(type="integer")
     */
    protected $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="Store\ProductBundle\Entity\Cart", inversedBy="items")
     */
    protected $cart;

    /**
     * Constructor
     *
     * @param int $quantity The bootstraped quantity for this CartItem
     * @return CartItem
     */
    public function __construct($quantity = 1)
    {
        $this->quantity = $quantity;
        return $this;
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
     * Set product
     *
     * @param \Store\ProductBundle\Entity\Product $product
     * @return CartItem
     */
    public function setProduct(\Store\ProductBundle\Entity\Variant $product = null)
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
     * Set cart
     *
     * @param \Store\ProductBundle\Entity\Cart $cart
     * @return CartItem
     */
    public function setCart(\Store\ProductBundle\Entity\Cart $cart = null)
    {
        $this->cart = $cart;
    
        return $this;
    }

    /**
     * Get cart
     *
     * @return \Store\ProductBundle\Entity\Cart 
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Get the quantity of this item
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Sets the quantity for an item
     *
     * @param int The quantity
     * @return CartItem
     */
    public function setQuantity($q = 1)
    {
        $this->quantity = $q;
        return $this;
    }

    /**
     * Increments the quantity by x
     *
     * @param int $quantity The defined quantity
     * @return CartItem
     */
    public function incrementQuantity($quantity)
    {
        $this->quantity = $this->quantity + $quantity;
        return $this;
    }
}