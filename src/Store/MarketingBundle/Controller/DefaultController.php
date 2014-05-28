<?php

namespace Store\MarketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('StoreMarketingBundle:Default:index.html.twig', array('name' => $name));
    }
}
