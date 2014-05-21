<?php

namespace Store\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('StorePaymentBundle:Default:index.html.twig', array('name' => $name));
    }
}
