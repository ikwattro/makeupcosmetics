<?php

namespace Store\CustomerBundle\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Store\CustomerBundle\Entity\Customer;
use Store\CustomerBundle\Entity\Profile;

class CustomerManager
{
    private $em;
    protected $context;

    public function __construct(EntityManager $em, SecurityContextInterface $context)
    {
        $this->em = $em;
        $this->context = $context;
    }

    public function getCustomer()
    {
        if(is_object($this->context->getToken())){
        if($this->context->getToken()->getUser()) {
            return $this->context->getToken()->getUser();
        }}
        return false;
    }

    public function getCustomerProfile()
    {
        if($this->getCustomer()) {
            if($this->getCustomer()->getProfile()){
                return $this->getCustomer()->getProfile();
            }
        }
        return false;
    }

    public function createCustomerProfile()
    {
        if($this->getCustomer() && !$this->getCustomerProfile()) {
            $profile = new Profile();
            $customer = $this->getCustomer();
            $customer->setProfile($profile);
            $this->em->persist($customer);
            $this->em->flush();
            return $customer;
        }
        return false;
    }

    public function updateCustomerProfile(Profile $profile)
    {
        $customer = $this->getCustomer();
        $customer->setProfile($profile);
        $this->em->persist($customer);
        $this->em->flush();
        return $profile;
    }
}