<?php

namespace Store\MarketingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CampaignController extends Controller
{


    private function sendCampaignEmail($followbackUrl, $email, $label, $subject, $template)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array('makeupcosmetics.eu@gmail.com' => 'MakeUp Cosmetics - Claude Haest'))
            ->setTo($email)
            ->setBody($this->renderView($template, array(
                'followback_url' => $followbackUrl,
                'email' => $email,
                'label' => $label,
            )), 'text/html')
        ;
        $this->get('mailer')->send($message);

        return true;
    }


    /**
     * @Route("/admin/campaign/email/send/endyear/{testOnly}", name="campaign_end_year")
     * @Tempate()
     */
    public function endYearCampaignAction($testOnly)
    {
        $targets = array();
        $template = 'StoreMarketingBundle:Campaign:endYearCampaign.html.twig'

        $test = $testOnly == 'reality' ? false : true;

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('StoreMarketingBundle:TargetEmail')->findAll();
        foreach ($entities as $target) {

                if ($test) {
                    if ($target->getTestAllowed()) {
                        $this->sendCampaignEmail($this->generateUrl('email_followback', array(), true), strtolower($target->getEmail()), 'endYear', 'Promo einde-fin 2014', $template);
                        $targets[] = $target->getEmail();
                    }
                } else {
                    $this->sendCampaignEmail($this->generateUrl('email_followback', array(), true), strtolower($target->getEmail()), 'endYear', 'Promo einde-fin 2014', $template);
                    $targets[] = $target->getEmail();
                }





        }

        return array(
            'count' => count($targets),
            'targets' => $targets
        );
    }

    /**
     * @Route("/admin/campaign/email/send/nl/promoFeutre/{testOnly}", name="campaign_send_nl_promoFeutre")
     * @Template()
     */
    public function sendPromoFeutreNlAction($testOnly)
    {
        $targets = array();

        $test = $testOnly == 'reality' ? false : true;

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('StoreMarketingBundle:TargetEmail')->findAll();

        var_dump(count($entities));

        foreach ($entities as $target) {
            if ($this->isValidForDutch($target)) {

                if ($test) {
                    if ($target->getTestAllowed()) {
                        //$this->sendCampaignEmail($this->generateUrl('email_followback', array(), true), strtolower($target->getEmail()), 'promoFeutre');
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

    /**
     * @Route("/admin/targets/dutch", name="admin_dutch_emails")
     * @Template()
     */
    public function dutchEmailsAction()
    {
        $targets = array();
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('StoreMarketingBundle:TargetEmail')->findAll();
        foreach ($entities as $target) {
            if ($this->isValidForDutch($target)) {
                $targets[] = $target->getEmail();
            }
        }
        return array(
            'targets' => $targets,
        );
    }

    public function isValidForDutch($target)
    {
        if ($target->getLanguage() == 'nl') {
            return true;
        }

        if (null !== $target->getLanguage() && ($target->getLanguage() == 'fr' || $target->getLanguage() == 'de')) {
            return false;
        }

        $email = $target->getEmail();
        $expl = explode('.', $email);
        $c = count($expl);
        $e = $expl[$c-1];
        if ($e == 'nl') {
            return true;
        }
        if (preg_match('/telenet/', $email)) {
            return true;
        }
        return false;
    }



}