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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $payment_state;

    /**
     * @ORM\ManyToOne(targetEntity="Store\AddressBundle\Entity\Address", cascade={"persist"})
     * @ORM\JoinColumn(name="billing_address_id", referencedColumnName="id", nullable=true)
     */
    protected $billing_address;

    /**
     * @ORM\ManyToOne(targetEntity="Store\AddressBundle\Entity\Address", cascade={"persist"})
     * @ORM\JoinColumn(name="shipping_address_id", referencedColumnName="id", nullable=true)
     */
    protected $shipping_address;

    /**
     * @ORM\ManyToOne(targetEntity="Store\CustomerBundle\Entity\Customer", inversedBy="carts")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true)
     */
    protected $customer;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $process_status;

    /**
     * @ORM\ManyToOne(targetEntity="Store\ShippingBundle\Entity\ShippingMethod")
     * @ORM\JoinColumn(name="shipping_method_id", referencedColumnName="id", nullable=true)
     */
    protected $shipping_method;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $shipping_price;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $promotion_discount;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $orderId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $userAgent;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isBot;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $orderProcessStatus;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orderProcessStatus = 'CART';
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

    /**
     * Set process_status
     *
     * @param integer $processStatus
     * @return Cart
     */
    public function setProcessStatus($processStatus)
    {
        $this->process_status = $processStatus;
    
        return $this;
    }

    /**
     * Get process_status
     *
     * @return integer 
     */
    public function getProcessStatus()
    {
        return $this->process_status;
    }

    /**
     * Set shipping_price
     *
     * @param float $shippingPrice
     * @return Cart
     */
    public function setShippingPrice($shippingPrice)
    {
        $this->shipping_price = $shippingPrice;
    
        return $this;
    }

    /**
     * Get shipping_price
     *
     * @return float 
     */
    public function getShippingPrice()
    {
        return $this->shipping_price;
    }

    /**
     * Set shipping_method
     *
     * @param \Store\ShippingBundle\Entity\ShippingMethod $shippingMethod
     * @return Cart
     */
    public function setShippingMethod(\Store\ShippingBundle\Entity\ShippingMethod $shippingMethod = null)
    {
        $this->shipping_method = $shippingMethod;
    
        return $this;
    }

    /**
     * Get shipping_method
     *
     * @return \Store\ShippingBundle\Entity\ShippingMethod 
     */
    public function getShippingMethod()
    {
        return $this->shipping_method;
    }

    /**
     * Set promotion_discount
     *
     * @param float $promotionDiscount
     *
     * @return Cart
     */
    public function setPromotionDiscount($promotionDiscount)
    {
        $this->promotion_discount = $promotionDiscount;

        return $this;
    }

    /**
     * Get promotion_discount
     *
     * @return float 
     */
    public function getPromotionDiscount()
    {
        return $this->promotion_discount;
    }

    /**
     * Set orderId
     *
     * @param string $orderId
     *
     * @return Cart
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return string 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set userAgent
     *
     * @param string $userAgent
     * @return Cart
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get userAgent
     *
     * @return string 
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set isBot
     *
     * @param boolean $isBot
     * @return Cart
     */
    public function setIsBot($isBot)
    {
        $this->isBot = $isBot;

        return $this;
    }

    /**
     * Get isBot
     *
     * @return boolean 
     */
    public function getIsBot()
    {
        return $this->isBot;
    }

    /**
     * Set orderProcessStatus
     *
     * @param string $orderProcessStatus
     * @return Cart
     */
    public function setOrderProcessStatus($orderProcessStatus)
    {
        $this->orderProcessStatus = $orderProcessStatus;

        return $this;
    }

    /**
     * Get orderProcessStatus
     *
     * @return string 
     */
    public function getOrderProcessStatus()
    {
        return $this->orderProcessStatus;
    }
}
