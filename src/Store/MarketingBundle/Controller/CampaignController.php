<?php

namespace Store\MarketingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CampaignController extends Controller
{
    /**
     * @Route("/campaign/email/newstart", name="campaign_newstart")
     * @Template()
     */
    public function newStartAction()
    {
        //var_dump($this->generateUrl('email_followback'));
        return array(
            'followback_url' => $this->generateUrl('email_followback'),
            'email' => 'willemsen.christophe@gmail.com'
        );
    }

    private function sendCampaignEmail($followbackUrl, $email)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('MakeUp Cosmetics de Claude Haest')
            ->setFrom(array('support@makeup-cosmetics.eu' => 'MakeUp Cosmetics.eu'))
            ->setTo($email)
            ->setBody($this->renderView('StoreMarketingBundle:Campaign:newStart.html.twig', array(
                'followback_url' => $followbackUrl,
                'email' => $email
            )), 'text/html')
        ;
        $this->get('mailer')->send($message);

        return true;
    }

    /**
     * @Route("/campaign/email/send/newStart", name="campaign_send_newstart")
     * @Template()
     */
    public function sendNewStartEmailAction()
    {
        $targets = array();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('StoreMarketingBundle:TargetEmail')->findAll();
        foreach ($entities as $target) {
            if ($target->getTestAllowed() == true) {
                $this->sendCampaignEmail($this->generateUrl('email_followback'), $target->getEmail());
                $targets[] = $target->getEmail();
            }
        }

        return array(
            'targets' => $targets
        );

    }


}