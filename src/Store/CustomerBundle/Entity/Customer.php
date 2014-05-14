<?php


namespace Store\CustomerBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Customer extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Store\ProductBundle\Entity\Cart", mappedBy="customer", cascade={"persist"})
     */
    protected $carts;

    /**
     * @ORM\OneToOne(targetEntity="Store\CustomerBundle\Entity\Profile", inversedBy="customer", cascade={"persist"})
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=true)
     */
    protected $profile;

    /**
     * @ORM\OneToMany(targetEntity="Store\AddressBundle\Entity\Address", mappedBy="customer", cascade={"persist"})
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id", nullable=true)
     */
    protected $addresses;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $preferredLanguage;


    public function __construct()
    {
        parent::__construct();
        // your own logic
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
     * Add carts
     *
     * @param \Store\ProductBundle\Entity\Cart $carts
     * @return Customer
     */
    public function addCart(\Store\ProductBundle\Entity\Cart $carts)
    {
        $this->carts[] = $carts;
    
        return $this;
    }

    /**
     * Remove carts
     *
     * @param \Store\ProductBundle\Entity\Cart $carts
     */
    public function removeCart(\Store\ProductBundle\Entity\Cart $carts)
    {
        $this->carts->removeElement($carts);
    }

    /**
     * Get carts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCarts()
    {
        return $this->carts;
    }

    /**
     * Set profile
     *
     * @param \Store\CustomerBundle\Entity\Profile $profile
     * @return Customer
     */
    public function setProfile(\Store\CustomerBundle\Entity\Profile $profile = null)
    {
        $this->profile = $profile;
    
        return $this;
    }

    /**
     * Get profile
     *
     * @return \Store\CustomerBundle\Entity\Profile 
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Add addresses
     *
     * @param \Store\AddressBundle\Entity\Address $addresses
     * @return Customer
     */
    public function addAddresse(\Store\AddressBundle\Entity\Address $addresses)
    {
        $this->addresses[] = $addresses;
    
        return $this;
    }

    /**
     * Remove addresses
     *
     * @param \Store\AddressBundle\Entity\Address $addresses
     */
    public function removeAddresse(\Store\AddressBundle\Entity\Address $addresses)
    {
        $this->addresses->removeElement($addresses);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Set preferredLanguage
     *
     * @param string $preferredLanguage
     * @return Customer
     */
    public function setPreferredLanguage($preferredLanguage)
    {
        $this->preferredLanguage = $preferredLanguage;
    
        return $this;
    }

    /**
     * Get preferredLanguage
     *
     * @return string 
     */
    public function getPreferredLanguage()
    {
        return $this->preferredLanguage;
    }
}