<?php

namespace Store\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Store\ProductBundle\Entity\OptionType;
use Store\ProductBundle\Form\OptionTypeType;

/**
 * OptionType controller.
 *
 * @Route("/optiontype")
 */
class OptionTypeController extends Controller
{

    /**
     * Lists all OptionType entities.
     *
     * @Route("/", name="optiontype")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('StoreProductBundle:OptionType')->findWithValues();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new OptionType entity.
     *
     * @Route("/", name="optiontype_create")
     * @Method("POST")
     * @Template("StoreProductBundle:OptionType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new OptionType();
        $form = $this->createForm(new OptionTypeType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('optiontype_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new OptionType entity.
     *
     * @Route("/new", name="optiontype_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new OptionType();
        $form   = $this->createForm(new OptionTypeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a OptionType entity.
     *
     * @Route("/{id}", name="optiontype_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:OptionType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OptionType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing OptionType entity.
     *
     * @Route("/{id}/edit", name="optiontype_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:OptionType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OptionType entity.');
        }

        $editForm = $this->createForm(new OptionTypeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing OptionType entity.
     *
     * @Route("/{id}", name="optiontype_update")
     * @Method("PUT")
     * @Template("StoreProductBundle:OptionType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:OptionType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OptionType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new OptionTypeType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('optiontype_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a OptionType entity.
     *
     * @Route("/{id}", name="optiontype_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('StoreProductBundle:OptionType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find OptionType entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('optiontype'));
    }

    /**
     * Creates a form to delete a OptionType entity by id.
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
