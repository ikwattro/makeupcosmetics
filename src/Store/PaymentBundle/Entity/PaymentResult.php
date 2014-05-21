<?php

namespace Store\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PaymentResult
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dtg;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $user;

    /**
     * @ORM\Column(type="string")
     */
    protected $orderId;

    /**
     * @ORM\Column(type="integer")
     */
    protected $responseStatus;

    /**
     * @ORM\Column(type="string")
     */
    protected $paymentPlatform;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $signatureValid;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $ip;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $brand;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $paymentValid;

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
     * Set dtg
     *
     * @param \DateTime $dtg
     *
     * @return PaymentResult
     */
    public function setDtg($dtg)
    {
        $this->dtg = $dtg;

        return $this;
    }

    /**
     * Get dtg
     *
     * @return \DateTime 
     */
    public function getDtg()
    {
        return $this->dtg;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return PaymentResult
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set orderId
     *
     * @param string $orderId
     *
     * @return PaymentResult
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
     * Set responseStatus
     *
     * @param integer $responseStatus
     *
     * @return PaymentResult
     */
    public function setResponseStatus($responseStatus)
    {
        $this->responseStatus = $responseStatus;

        return $this;
    }

    /**
     * Get responseStatus
     *
     * @return integer 
     */
    public function getResponseStatus()
    {
        return $this->responseStatus;
    }

    /**
     * Set paymentPlatform
     *
     * @param string $paymentPlatform
     *
     * @return PaymentResult
     */
    public function setPaymentPlatform($paymentPlatform)
    {
        $this->paymentPlatform = $paymentPlatform;

        return $this;
    }

    /**
     * Get paymentPlatform
     *
     * @return string 
     */
    public function getPaymentPlatform()
    {
        return $this->paymentPlatform;
    }

    /**
     * Set signatureValid
     *
     * @param boolean $signatureValid
     *
     * @return PaymentResult
     */
    public function setSignatureValid($signatureValid)
    {
        $this->signatureValid = $signatureValid;

        return $this;
    }

    /**
     * Get signatureValid
     *
     * @return boolean 
     */
    public function getSignatureValid()
    {
        return $this->signatureValid;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return PaymentResult
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set brand
     *
     * @param string $brand
     *
     * @return PaymentResult
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return string 
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set paymentValid
     *
     * @param boolean $paymentValid
     *
     * @return PaymentResult
     */
    public function setPaymentValid($paymentValid)
    {
        $this->paymentValid = $paymentValid;

        return $this;
    }

    /**
     * Get paymentValid
     *
     * @return boolean 
     */
    public function getPaymentValid()
    {
        return $this->paymentValid;
    }
}
