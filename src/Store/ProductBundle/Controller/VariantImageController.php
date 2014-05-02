<?php

namespace Store\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Store\ProductBundle\Entity\VariantImage;
use Store\ProductBundle\Form\VariantImageType;

/**
 * VariantImage controller.
 *
 * @Route("/variantimage")
 */
class VariantImageController extends Controller
{

    /**
     * Lists all VariantImage entities.
     *
     * @Route("/", name="variantimage")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('StoreProductBundle:VariantImage')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new VariantImage entity.
     *
     * @Route("/", name="variantimage_create")
     * @Method("POST")
     * @Template("StoreProductBundle:VariantImage:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new VariantImage();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('variantimage_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a VariantImage entity.
    *
    * @param VariantImage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(VariantImage $entity)
    {
        $form = $this->createForm(new VariantImageType(), $entity, array(
            'action' => $this->generateUrl('variantimage_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new VariantImage entity.
     *
     * @Route("/new/{variant}", name="variantimage_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($variant)
    {
        $em = $this->getDoctrine()->getManager();
        $var = $em->getRepository('StoreProductBundle:Variant')->find($variant);
        if(!$var){
            throw $this->createNotFoundException('Unabel to find Variant Entity');
        }
        $entity = new VariantImage();
        $entity->setVariant($var);
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a VariantImage entity.
     *
     * @Route("/{id}", name="variantimage_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:VariantImage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find VariantImage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing VariantImage entity.
     *
     * @Route("/{id}/edit", name="variantimage_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:VariantImage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find VariantImage entity.');
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
    * Creates a form to edit a VariantImage entity.
    *
    * @param VariantImage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(VariantImage $entity)
    {
        $form = $this->createForm(new VariantImageType(), $entity, array(
            'action' => $this->generateUrl('variantimage_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing VariantImage entity.
     *
     * @Route("/{id}", name="variantimage_update")
     * @Method("PUT")
     * @Template("StoreProductBundle:VariantImage:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:VariantImage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find VariantImage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('variantimage_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a VariantImage entity.
     *
     * @Route("/{id}", name="variantimage_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('StoreProductBundle:VariantImage')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find VariantImage entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('variantimage'));
    }

    /**
     * Creates a form to delete a VariantImage entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('variantimage_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
