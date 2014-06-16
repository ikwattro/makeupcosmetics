<?php

namespace Store\CoreBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationListener implements EventSubscriberInterface
{
    private $router;

    private $request;

    private $session;

    private $context;

    private $em;

    public function __construct(UrlGeneratorInterface $router, Request $request, SessionInterface $session)
    {
        $this->router = $router;
        $this->request = $request;
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInit',
        );
    }

    public function onRegistrationSuccess(FormEvent $event)
    {


        $url = $this->router->generate('checkout_account');
        if($this->session->get('from_checkout', null)){
            $this->session->set('from_checkout', false);
            $event->setResponse(new RedirectResponse($url));
        }

    }

    public function onRegistrationInit(GetResponseUserEvent $event)
    {
        $referer = $this->request->headers->get('referer');
        if(preg_match('/checkout/', $referer)) {
            $this->session->set('from_checkout', true);
        } else {
            if(!preg_match('/register/', $referer)) {
                $this->session->set('from_checkout', false);
            }
        }
    }
}