<?php

namespace Store\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Store\ProductBundle\Entity\Category;
use Store\ProductBundle\Form\CategoryType;
use URLify;

/**
 * Category controller.
 *
 * @Route("/category")
 */
class CategoryController extends Controller
{

    /**
     * Lists all Category entities.
     *
     * @Route("/", name="category")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('StoreProductBundle:Category')->findAllByLocaleWithChildren('fr', true);

        return array(
            'entities' => $entities,
        );
    }


    /**
     * @Template()
     */
    public function showForProductAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Category')->findByLocale($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        return array(
            'entity' => $entity
        );
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/", name="category_create")
     * @Method("POST")
     * @Template("StoreProductBundle:Category:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Category();
        $form = $this->createForm(new CategoryType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $entity->setTranslatableLocale('fr');
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('category_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Category entity.
     *
     * @Route("/new", name="category_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Category();
        $form   = $this->createForm(new CategoryType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Route("/{id}", name="category_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Category')->findByLocale($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $repository = $em->getRepository('StoreProductBundle:CategoryEntityTranslation');
        $translations = $repository->findTranslations($entity);

        $entity->setTranslations($translations);

        //var_dump( (array) $entity->getTranslations());

        /**
        $entity->setTitle('gezicht zorgen hohohohohohohoho');
        $entity->setSlug(URLify::filter($entity->getTitle()));
        $entity->setTranslatableLocale('nl');
        $em->persist($entity);
        $em->flush();

        exit();
         */

        $path = $em->getRepository('StoreProductBundle:Category')->getPath($entity);
        $xpath = '';
        $i = 1;
        $count = count($path);
        foreach($path as $node) {
            $xpath .= $node->getTitle();
            if($i < $count){
                $xpath .= ' > ';
                $i++;
            }
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'xpath'     => $xpath,
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="category_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Category')->findByLocale($id, 'fr');

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        var_dump($entity->getTranslatableLocale());
        var_dump($entity->getTitle());
        var_dump($entity->getId());
        var_dump($entity->getTranslations());

        $editForm = $this->createForm(new CategoryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/{locale}/edit", name="category_edit_translation")
     * @Method("GET")
     * @Template()
     */
    public function editTranslationAction($id, $locale)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Category')->findByLocale($id, $locale);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $editForm = $this->createForm(new CategoryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'locale'    => $locale,
        );
    }

    /**
     * Edits an existing Category entity.
     *
     * @Route("/{id}/editcat/loc/{locale}", name="category_update", defaults={"locale" = null})
     * @Method("PUT")
     * @Template("StoreProductBundle:Category:edit.html.twig")
     */
    public function updateAction(Request $request, $id, $locale = null)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Category')->findByLocale($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        $entity->setTranslatableLocale('fr');
        if(null !== $locale){
            $entity->setTranslatableLocale($locale);
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CategoryType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            if(null !== $locale){
                $entity->setSlug(URLify::filter($entity->getTitle()));
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('category_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}", name="category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('StoreProductBundle:Category')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Category entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('category'));
    }

    /**
     * Creates a form to delete a Category entity by id.
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
