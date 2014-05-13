<?php

namespace Store\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity(repositoryClass="Store\ProductBundle\Repository\CartRepository")
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
     * @ORM\OneToMany(targetEntity="Store\ProductBundle\Entity\CartItem", mappedBy="cart", cascade={"persist", "remove"})
     */
    protected $items;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $cart_dtg;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $number;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $items_total;

    /**
     * @ORM\Column(type="string")
     */
    protected $state;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $user_id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $payment_state;

    /**
     * @ORM\ManyToOne(targetEntity="Store\AddressBundle\Entity\Address")
     * @ORM\JoinColumn(name="billing_address_id", referencedColumnName="id", nullable=true)
     */
    protected $billing_address;

    /**
     * @ORM\ManyToOne(targetEntity="Store\AddressBundle\Entity\Address")
     * @ORM\JoinColumn(name="shipping_address_id", referencedColumnName="id", nullable=true)
     */
    protected $shipping_address;

    /**
     * @ORM\ManyToOne(targetEntity="Store\CustomerBundle\Entity\Customer", inversedBy="carts")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true)
     */
    protected $customer;

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

    /**
     * Set number
     *
     * @param integer $number
     * @return Cart
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set items_total
     *
     * @param integer $itemsTotal
     * @return Cart
     */
    public function setItemsTotal($itemsTotal)
    {
        $this->items_total = $itemsTotal;
    
        return $this;
    }

    /**
     * Get items_total
     *
     * @return integer 
     */
    public function getItemsTotal()
    {
        return $this->items_total;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Cart
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Cart
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     * @return Cart
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    
        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set payment_state
     *
     * @param string $paymentState
     * @return Cart
     */
    public function setPaymentState($paymentState)
    {
        $this->payment_state = $paymentState;
    
        return $this;
    }

    /**
     * Get payment_state
     *
     * @return string 
     */
    public function getPaymentState()
    {
        return $this->payment_state;
    }

    /**
     * Set billing_address
     *
     * @param \Store\AddressBundle\Entity\Address $billingAddress
     * @return Cart
     */
    public function setBillingAddress(\Store\AddressBundle\Entity\Address $billingAddress = null)
    {
        $this->billing_address = $billingAddress;
    
        return $this;
    }

    /**
     * Get billing_address
     *
     * @return \Store\AddressBundle\Entity\Address 
     */
    public function getBillingAddress()
    {
        return $this->billing_address;
    }

    /**
     * Set shipping_address
     *
     * @param \Store\AddressBundle\Entity\Address $shippingAddress
     * @return Cart
     */
    public function setShippingAddress(\Store\AddressBundle\Entity\Address $shippingAddress = null)
    {
        $this->shipping_address = $shippingAddress;
    
        return $this;
    }

    /**
     * Get shipping_address
     *
     * @return \Store\AddressBundle\Entity\Address 
     */
    public function getShippingAddress()
    {
        return $this->shipping_address;
    }

    /**
     * Set customer
     *
     * @param \Store\CustomerBundle\Entity\Customer $customer
     * @return Cart
     */
    public function setCustomer(\Store\CustomerBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;
    
        return $this;
    }

    /**
     * Get customer
     *
     * @return \Store\CustomerBundle\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}