<?php

namespace Store\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Store\ProductBundle\Entity\OptionValue;
use Store\ProductBundle\Form\OptionValueType;

/**
 * OptionValue controller.
 *
 * @Route("/optionvalue")
 */
class OptionValueController extends Controller
{

    /**
     * Lists all OptionValue entities.
     *
     * @Route("/", name="optionvalue")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('StoreProductBundle:OptionValue')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new OptionValue entity.
     *
     * @Route("/", name="optionvalue_create")
     * @Method("POST")
     * @Template("StoreProductBundle:OptionValue:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new OptionValue();
        $form = $this->createForm(new OptionValueType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('optionvalue_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/add_value_for_optiontype/{option}", name="optionvalue_addforoption")
     * @Method("POST")
     *
     */
    public function addForOption(Request $request, $option)
    {
        $value = $request->request->get('value');
        $em = $this->getDoctrine()->getManager();
        $opt = $em->getRepository('StoreProductBundle:OptionType')->find($option);

        if (!$opt) {
            throw $this->createNotFoundException('Unable to find OptionValue entity.');
        }

        $optionValue = new OptionValue();
        $optionValue->setName($value);
        $optionValue->setOption($opt);

        $em->persist($optionValue);
        $em->flush();

        return $this->redirect($this->generateUrl('optiontype_show', array('id' => $opt->getId())));

    }

    /**
     * Displays a form to create a new OptionValue entity.
     *
     * @Route("/new", name="optionvalue_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new OptionValue();
        $form   = $this->createForm(new OptionValueType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a OptionValue entity.
     *
     * @Route("/{id}", name="optionvalue_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:OptionValue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OptionValue entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing OptionValue entity.
     *
     * @Route("/{id}/edit", name="optionvalue_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:OptionValue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OptionValue entity.');
        }

        $editForm = $this->createForm(new OptionValueType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing OptionValue entity.
     *
     * @Route("/{id}", name="optionvalue_update")
     * @Method("PUT")
     * @Template("StoreProductBundle:OptionValue:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:OptionValue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find OptionValue entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new OptionValueType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('optionvalue_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a OptionValue entity.
     *
     * @Route("/{id}", name="optionvalue_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('StoreProductBundle:OptionValue')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find OptionValue entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('optionvalue'));
    }

    /**
     * Creates a form to delete a OptionValue entity by id.
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
