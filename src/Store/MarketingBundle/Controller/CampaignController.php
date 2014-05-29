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
            ->setFrom(array('makeupcosmetics.eu@gmail.com' => 'MakeUp Cosmetics.eu'))
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
            if ($this->isValidForFrench($target)) {
                //$this->sendCampaignEmail($this->generateUrl('email_followback'), $target->getEmail());
                $targets[] = $target->getEmail();
            }
        }

        return array(
            'targets' => $targets
        );

    }

    public function isValidForFrench($target)
    {
        if ($target->getLanguage() == 'fr') {
            return true;
        }

        $email = $target->getEmail();
        $expl = explode('.', $email);
        $c = count($expl);
        $e = $expl[$c-1];
        if ($e == 'nl' || $e == 'de') {
            return false;
        }
        if (preg_match('/telenet/', $email)) {
            return false;
        }
        return true;
    }


}