<?php

namespace Store\MarketingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Store\MarketingBundle\Entity\TargetEmail;
use Store\MarketingBundle\Form\TargetEmailType;

/**
 * TargetEmail controller.
 *
 * @Route("/targetemail")
 */
class TargetEmailController extends Controller
{

    /**
     * Lists all TargetEmail entities.
     *
     * @Route("/", name="targetemail")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('StoreMarketingBundle:TargetEmail')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new TargetEmail entity.
     *
     * @Route("/", name="targetemail_create")
     * @Method("POST")
     * @Template("StoreMarketingBundle:TargetEmail:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TargetEmail();
        $entity->setSubscribedAt(new \DateTime("NOW"));
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('targetemail_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a TargetEmail entity.
     *
     * @param TargetEmail $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TargetEmail $entity)
    {
        $form = $this->createForm(new TargetEmailType(), $entity, array(
            'action' => $this->generateUrl('targetemail_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new TargetEmail entity.
     *
     * @Route("/new", name="targetemail_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TargetEmail();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a TargetEmail entity.
     *
     * @Route("/{id}", name="targetemail_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreMarketingBundle:TargetEmail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TargetEmail entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing TargetEmail entity.
     *
     * @Route("/{id}/edit", name="targetemail_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreMarketingBundle:TargetEmail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TargetEmail entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a TargetEmail entity.
    *
    * @param TargetEmail $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TargetEmail $entity)
    {
        $form = $this->createForm(new TargetEmailType(), $entity, array(
            'action' => $this->generateUrl('targetemail_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TargetEmail entity.
     *
     * @Route("/{id}", name="targetemail_update")
     * @Method("PUT")
     * @Template("StoreMarketingBundle:TargetEmail:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreMarketingBundle:TargetEmail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TargetEmail entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('targetemail_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a TargetEmail entity.
     *
     * @Route("/{id}", name="targetemail_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('StoreMarketingBundle:TargetEmail')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TargetEmail entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('targetemail'));
    }

    /**
     * Creates a form to delete a TargetEmail entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('targetemail_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * @Route("/import/csv", name="targetemail_import")
     * @Template()
     */
    public function importAction()
    {
        $importResult = array();

        $em = $this->getDoctrine()->getManager();

        $i = 1;

        $user_file = $this->get('kernel')->getRootDir() . '/../web/users2.csv';
        $csv = array_map('str_getcsv', file($user_file));
        foreach($csv as $user){
            $name = $user[0];
            $email = $user[1];
            $language = $user[2];
            if($email != null){
                if ($em->getRepository('StoreMarketingBundle:TargetEmail')->findByEmail($email)) {
                    $importResult[] = array('action' => 'idle', 'email' => $email, 'language' => $language);
                } else {
                    $target = new TargetEmail();
                    $target->setEmail($email);
                    $target->setLanguage($language);
                    $target->setSubscribed(true);
                    $target->setSubscribedAt(new \DateTime("NOW"));
                    $em->persist($target);
                    $em->flush();
                    $importResult[] = array('action' => 'insert', 'email' => $email, 'language' => $language);
                }

            }
        }

        return array(
            'import' => $importResult
        );
    }

    /**
     * @Route("/allowtest/{id}", name="targetemail_allowtest")
     */
    public function allowTestAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$em->getRepository('StoreMarketingBundle:TargetEmail')->find($id)) {
            throw new \InvalidArgumentException('TargetEmail not found');
        }

        $target = $em->getRepository('StoreMarketingBundle:TargetEmail')->find($id);
        $target->setTestAllowed(true);
        $em->persist($target);
        $em->flush();
        return $this->redirect($this->generateUrl('targetemail'));
    }

    /**
     * @Route("/allowtest/{id}", name="targetemail_disallowtest")
     */
    public function disallowTestAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$em->getRepository('StoreMarketingBundle:TargetEmail')->find($id)) {
            throw new \InvalidArgumentException('TargetEmail not found');
        }

        $target = $em->getRepository('StoreMarketingBundle:TargetEmail')->find($id);
        $target->setTestAllowed(false);
        $em->persist($target);
        $em->flush();
        return $this->redirect($this->generateUrl('targetemail'));
    }
}
