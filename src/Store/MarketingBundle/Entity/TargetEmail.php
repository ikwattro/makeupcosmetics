<?php

namespace Store\MarketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TargetEmail
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
    protected $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $language;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $subscribed;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $subscribedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $unsubscribedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $testAllowed;

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
     * Set email
     *
     * @param string $email
     * @return TargetEmail
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
     * Set language
     *
     * @param string $language
     * @return TargetEmail
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set subscribed
     *
     * @param boolean $subscribed
     * @return TargetEmail
     */
    public function setSubscribed($subscribed)
    {
        $this->subscribed = $subscribed;

        return $this;
    }

    /**
     * Get subscribed
     *
     * @return boolean 
     */
    public function getSubscribed()
    {
        return $this->subscribed;
    }

    /**
     * Set subscribedAt
     *
     * @param \DateTime $subscribedAt
     * @return TargetEmail
     */
    public function setSubscribedAt($subscribedAt)
    {
        $this->subscribedAt = $subscribedAt;

        return $this;
    }

    /**
     * Get subscribedAt
     *
     * @return \DateTime 
     */
    public function getSubscribedAt()
    {
        return $this->subscribedAt;
    }

    /**
     * Set unsubscribedAt
     *
     * @param \DateTime $unsubscribedAt
     * @return TargetEmail
     */
    public function setUnsubscribedAt($unsubscribedAt)
    {
        $this->unsubscribedAt = $unsubscribedAt;

        return $this;
    }

    /**
     * Get unsubscribedAt
     *
     * @return \DateTime 
     */
    public function getUnsubscribedAt()
    {
        return $this->unsubscribedAt;
    }

    /**
     * Set testAllowed
     *
     * @param boolean $testAllowed
     * @return TargetEmail
     */
    public function setTestAllowed($testAllowed)
    {
        $this->testAllowed = $testAllowed;

        return $this;
    }

    /**
     * Get testAllowed
     *
     * @return boolean 
     */
    public function getTestAllowed()
    {
        return $this->testAllowed;
    }
}
