<?php

namespace Store\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/messages", name="admin_messages_list")
     * @Template()
     */
    public function listMessagesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $messages = $em->getRepository('StoreContactBundle:Message')->findAll();

        return array(
            'messages' => $messages,
        );
    }

    /**
     * @Route("/message/{id}/view", name="admin_message_view")
     * @Template()
     */
    public function viewMessageAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $message = $em->getRepository('StoreContactBundle:Message')->find($id);

        if (!$message) {
            throw new \InvalidArgumentException('Message Not Found');
        }

        return array(
            'message' => $message,
        );
    }

}
