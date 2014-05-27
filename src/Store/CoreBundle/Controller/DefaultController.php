<?php

namespace Store\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="admin_home")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('StoreProductBundle:Variant')->findAll();

        return array(
            'numberOfProducts'  =>  count($products),
        );
    }

    /**
     * @Route("/customers", name="admin_customers")
     * @Template()
     */
    public function customersAction()
    {
        $em = $this->getDoctrine()->getManager();

        $customers = $em->getRepository('StoreCustomerBundle:Customer')->findAll();

        return array(
            'customers' => $customers
        );
    }
}
