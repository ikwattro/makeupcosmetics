<?php

namespace Store\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 */
class Cart
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $sessionId;

    /**
     * @ORM\OneToMany(targetEntity="Store\ProductBundle\Entity\CartItem", mappedBy="cart", cascade={"persist"})
     */
    protected $items;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $cart_dtg;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get Timestamp of Cart creation
     *
     * @return mixed
     */
    public function getCartDtg()
    {
        return $this->cart_dtg;
    }

    /**
     * @param \DateTime $dtg
     */
    public function setCartDtg($dtg)
    {
        $this->cart_dtg = $dtg;
    }

    /**
     * Set sessionId
     *
     * @param string $sessionId
     * @return Cart
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    
        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string 
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Add items
     *
     * @param \Store\ProductBundle\Entity\CartItem $items
     * @return Cart
     */
    public function addItem(\Store\ProductBundle\Entity\CartItem $items)
    {
        $this->items[] = $items;
    
        return $this;
    }

    /**
     * Remove items
     *
     * @param \Store\ProductBundle\Entity\CartItem $items
     */
    public function removeItem(\Store\ProductBundle\Entity\CartItem $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        return $this->items;
    }
}