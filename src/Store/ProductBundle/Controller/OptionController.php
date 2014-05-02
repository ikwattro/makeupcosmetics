<?php

namespace Store\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Store\ProductBundle\Entity\Option;
use Store\ProductBundle\Form\OptionType;

/**
 * Option controller.
 *
 * @Route("/option")
 */
class OptionController extends Controller
{

    /**
     * Lists all Option entities.
     *
     * @Route("/", name="option")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('StoreProductBundle:Option')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Option entity.
     *
     * @Route("/", name="option_create")
     * @Method("POST")
     * @Template("StoreProductBundle:Option:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Option();
        $form = $this->createForm(new OptionType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('option_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Option entity.
     *
     * @Route("/new", name="option_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Option();
        $form   = $this->createForm(new OptionType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Option entity.
     *
     * @Route("/{id}", name="option_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Option')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Option entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Option entity.
     *
     * @Route("/{id}/edit", name="option_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Option')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Option entity.');
        }

        $editForm = $this->createForm(new OptionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Option entity.
     *
     * @Route("/{id}", name="option_update")
     * @Method("PUT")
     * @Template("StoreProductBundle:Option:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Option')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Option entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new OptionType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('option_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Option entity.
     *
     * @Route("/{id}", name="option_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('StoreProductBundle:Option')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Option entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('option'));
    }

    /**
     * Creates a form to delete a Option entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
