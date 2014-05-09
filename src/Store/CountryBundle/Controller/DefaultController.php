<?php

namespace Store\CountryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('StoreCountryBundle:Default:index.html.twig', array('name' => $name));
    }
}
