<?php

namespace Store\ShippingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Store\ShippingBundle\Entity\ShippingMethod;
use Store\ShippingBundle\Form\ShippingMethodType;

/**
 * ShippingMethod controller.
 *
 * @Route("/shippingmethod")
 */
class ShippingMethodController extends Controller
{

    /**
     * Lists all ShippingMethod entities.
     *
     * @Route("/", name="shippingmethod")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('StoreShippingBundle:ShippingMethod')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new ShippingMethod entity.
     *
     * @Route("/", name="shippingmethod_create")
     * @Method("POST")
     * @Template("StoreShippingBundle:ShippingMethod:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ShippingMethod();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('shippingmethod_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ShippingMethod entity.
     *
     * @param ShippingMethod $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ShippingMethod $entity)
    {
        $form = $this->createForm(new ShippingMethodType(), $entity, array(
            'action' => $this->generateUrl('shippingmethod_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ShippingMethod entity.
     *
     * @Route("/new", name="shippingmethod_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new ShippingMethod();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ShippingMethod entity.
     *
     * @Route("/{id}", name="shippingmethod_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreShippingBundle:ShippingMethod')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ShippingMethod entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ShippingMethod entity.
     *
     * @Route("/{id}/edit", name="shippingmethod_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreShippingBundle:ShippingMethod')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ShippingMethod entity.');
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
    * Creates a form to edit a ShippingMethod entity.
    *
    * @param ShippingMethod $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ShippingMethod $entity)
    {
        $form = $this->createForm(new ShippingMethodType(), $entity, array(
            'action' => $this->generateUrl('shippingmethod_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ShippingMethod entity.
     *
     * @Route("/{id}", name="shippingmethod_update")
     * @Method("PUT")
     * @Template("StoreShippingBundle:ShippingMethod:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreShippingBundle:ShippingMethod')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ShippingMethod entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('shippingmethod_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ShippingMethod entity.
     *
     * @Route("/{id}", name="shippingmethod_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('StoreShippingBundle:ShippingMethod')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ShippingMethod entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('shippingmethod'));
    }

    /**
     * Creates a form to delete a ShippingMethod entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('shippingmethod_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
