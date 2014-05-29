<?php

namespace Store\CustomerBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Store\CustomerBundle\Entity\Profile;
use Store\CustomerBundle\Form\ProfileType;

/**
 * Profile controller.
 *
 * @Route("/myaccount")
 */
class MyAccountController extends Controller
{

    /**
     * @Route("/", name="myaccount_home")
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();

        $orders = $this->get('store.store_manager')->getOrdersForUser($user);

        return array(
            'orders' => $orders,
            'profile' => $user->getProfile(),
        );
    }
}