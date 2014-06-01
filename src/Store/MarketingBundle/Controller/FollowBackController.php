<?php

namespace Store\MarketingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Store\MarketingBundle\Entity\FollowBack;

class FollowBackController extends Controller
{
    /**
     * @Route("/campaign/email/followback", name="email_followback")
     */
    public function emailFollowBack(Request $request)
    {
        $params = $request->query->all();

        $actionTargets = array(
            'site' => $this->generateUrl('homeweb'),
            'facebook' => 'https://www.facebook.com/pages/MakeUp-Cosmetics-Professional/643352275751858',
            'twitter' => 'https://twitter.com/mUpCosmetics',
            'promoFm' => 'http://www.makeup-cosmetics.eu/catalog/product/37/french-manucure',
        );

        if (!isset($params['targetEmail']) || !isset($params['campaignLabel'])) {
            return $this->redirect($this->generateUrl('homeweb'));
        }

        $em = $this->getDoctrine()->getManager();

        $follow = new FollowBack();

        $follow->setDtg(new \DateTime("NOW"));
        $follow->setCampaignLabel('newStart');
        $follow->setAction($params['action']);
        $follow->setTargetEmail($params['targetEmail']);
        $follow->setBrowserLanguage($request->getPreferredLanguage());

        $em->persist($follow);
        $em->flush();

        return $this->redirect($actionTargets[$params['action']]);

    }

    /**
     * @Route("/admin/followback", name="admin_followback")
     * @Template()
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('StoreMarketingBundle:FollowBack')->findAllDesc();

        return array(
            'follows' => $entities,
        );
    }

}