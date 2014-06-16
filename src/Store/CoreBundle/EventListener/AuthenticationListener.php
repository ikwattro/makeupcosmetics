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
    private $request;
    private $context;
    private $em;

    public function __construct(CustomerManager $customerManager, $context, $request, $em)
    {
        $this->customerManager = $customerManager;
        $this->request = $request;
        $this->context = $context;
        $this->em = $em;
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
        $locale = $this->request->getPreferredLanguage();
        $user = $this->context->getToken()->getUser();
        $user->setPreferredLanguage($locale);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function onImplicitLogin(UserEvent $event)
    {
        $this->customerManager->createCustomerProfile();
        $locale = $this->request->getPreferredLanguage();
        $user = $this->context->getToken()->getUser();
        $user->setPreferredLanguage($locale);
        $this->em->persist($user);
        $this->em->flush();
    }
}