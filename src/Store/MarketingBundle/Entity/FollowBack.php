<?php

namespace Store\MarketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FollowBack
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
    protected $campaignLabel;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $dtg;

    /**
     * @ORM\Column(type="string")
     */
    protected $action;

    /**
     * @ORM\Column(type="string")
     */
    protected $targetEmail;

    /**
     * @ORM\Column(type="string")
     */
    protected $browserLanguage;

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
     * Set campaignLabel
     *
     * @param string $campaignLabel
     * @return FollowBack
     */
    public function setCampaignLabel($campaignLabel)
    {
        $this->campaignLabel = $campaignLabel;

        return $this;
    }

    /**
     * Get campaignLabel
     *
     * @return string 
     */
    public function getCampaignLabel()
    {
        return $this->campaignLabel;
    }

    /**
     * Set dtg
     *
     * @param \DateTime $dtg
     * @return FollowBack
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
     * Set action
     *
     * @param string $action
     * @return FollowBack
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set targetEmail
     *
     * @param string $targetEmail
     * @return FollowBack
     */
    public function setTargetEmail($targetEmail)
    {
        $this->targetEmail = $targetEmail;

        return $this;
    }

    /**
     * Get targetEmail
     *
     * @return string 
     */
    public function getTargetEmail()
    {
        return $this->targetEmail;
    }

    /**
     * Set browserLanguage
     *
     * @param string $browserLanguage
     * @return FollowBack
     */
    public function setBrowserLanguage($browserLanguage)
    {
        $this->browserLanguage = $browserLanguage;

        return $this;
    }

    /**
     * Get browserLanguage
     *
     * @return string 
     */
    public function getBrowserLanguage()
    {
        return $this->browserLanguage;
    }
}
