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
            'followback_url' => $this->generateUrl('email_followback', array(), true),
            'email' => 'willemsen.christophe@gmail.com'
        );
    }

    /**
     * @Route("/campaign/email/promoFm", name="campaign_promoFm")
     * @Template()
     */
    public function promoFmAction()
    {
        //var_dump($this->generateUrl('email_followback'));
        $em = $this->get('request')->query->get('targetEmail') ?: '';
        return array(
            'followback_url' => $this->generateUrl('email_followback', array(), true),
            'email' => $em,
        );
    }

    /**
     * @Route("/campaign/email/promoFeutre", name="campaign_promo_feutre")
     * @Template()
     */
    public function promoFeutreAction()
    {
        //var_dump($this->generateUrl('email_followback'));
        $em = $this->get('request')->query->get('targetEmail') ?: '';
        return array(
            'followback_url' => $this->generateUrl('email_followback', array(), true),
            'email' => $em,
            'label' => 'promoFeutre'
        );
    }

    private function sendCampaignEmail($followbackUrl, $email, $label)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Promo Feutre à Lèvres, 2 pour le prix d\'1')
            ->setFrom(array('makeupcosmetics.eu@gmail.com' => 'MakeUp Cosmetics.eu'))
            ->setTo($email)
            ->setBody($this->renderView('StoreMarketingBundle:Campaign:promoFeutre.html.twig', array(
                'followback_url' => $followbackUrl,
                'email' => $email,
                'label' => $label,
            )), 'text/html')
        ;
        $this->get('mailer')->send($message);

        return true;
    }

    /**
     * @Route("/admin/campaign/email/send/newStart", name="campaign_send_newstart")
     * @Template()
     */
    public function sendNewStartEmailAction()
    {
        $targets = array();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('StoreMarketingBundle:TargetEmail')->findAll();
        foreach ($entities as $target) {
            if ($this->isValidForFrench($target)) {
                //$this->sendCampaignEmail($this->generateUrl('email_followback'), strtolower($target->getEmail()));
                $targets[] = $target->getEmail();
            }
        }

        return array(
            'count' => count($targets),
            'targets' => $targets
        );

    }

    /**
     * @Route("/admin/campaign/email/send/promoFm", name="campaign_send_promoFm")
     * @Template()
     */
    public function sendPromoFmAction()
    {
        $targets = array();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('StoreMarketingBundle:TargetEmail')->findAll();
        foreach ($entities as $target) {
            if ($this->isValidForFrench($target)) {
                $this->sendCampaignEmail($this->generateUrl('email_followback', array(), true), strtolower($target->getEmail()));
                $targets[] = $target->getEmail();
            }
        }

        return array(
            'count' => count($targets),
            'targets' => $targets
        );

    }

    /**
     * @Route("/admin/campaign/email/send/promoFeutre/{testOnly}", name="campaign_send_promoFeutre")
     * @Template()
     */
    public function sendPromoFeutreAction($testOnly)
    {
        $targets = array();

        $test = $testOnly == 'reality' ? false : true;

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('StoreMarketingBundle:TargetEmail')->findAll();
        foreach ($entities as $target) {
            if ($this->isValidForFrench($target)) {

                if ($test) {
                    if ($target->getTestAllowed()) {
                        $this->sendCampaignEmail($this->generateUrl('email_followback', array(), true), strtolower($target->getEmail()), 'promoFeutre');
                        $targets[] = $target->getEmail();
                    }
                } else {
                    //$this->sendCampaignEmail($this->generateUrl('email_followback', array(), true), strtolower($target->getEmail()), 'promoFeutre');
                    $targets[] = $target->getEmail();
                }




            }
        }

        return array(
            'count' => count($targets),
            'targets' => $targets
        );

    }

    public function isValidForFrench($target)
    {
        if ($target->getLanguage() == 'fr') {
            return true;
        }

        if (null !== $target->getLanguage() && ($target->getLanguage() =='nl' || $target->getLanguage() == 'de')) {
            return false;
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