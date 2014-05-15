<?php

namespace Store\CoreBundle\EventListener;

use Store\CustomerBundle\Entity\Customer;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Store\CustomerBundle\Manager\CustomerManager;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\UserEvent;

class AuthenticationListener implements EventSubscriberInterface
{
    private $customerManager;

    public function __construct(CustomerManager $customerManager)
    {
        $this->customerManager = $customerManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
            SecurityEvents::INTERACTIVE_LOGIN => 'onAuthenticationSuccess',
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin'
        );
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {

    }

    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $this->customerManager->createCustomerProfile();
    }

    public function onImplicitLogin(UserEvent $event)
    {
        $this->customerManager->createCustomerProfile();
    }
}