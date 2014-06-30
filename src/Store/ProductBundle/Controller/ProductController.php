<?php

namespace Store\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Store\ProductBundle\Entity\Product;
use Store\ProductBundle\Entity\ProductEntityTranslation;
use Store\ProductBundle\Form\ProductType;
use Store\ProductBundle\Form\ProductTranslationType;
use URLify;
use Store\ProductBundle\Entity\Variant;

/**
 * Product controller.
 *
 * @Route("/product")
 */
class ProductController extends Controller
{

    /**
     * Lists all Product entities.
     *
     * @Route("/", name="product")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('StoreProductBundle:Product')->findAllByLocale(null, null, false);

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Product entity.
     *
     * @Route("/", name="product_create")
     * @Method("POST")
     * @Template("StoreProductBundle:Product:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Product();
        $form = $this->createForm(new ProductType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $variant = new Variant();
            $variant->setIsMaster(true);
            $variant->setPrice($form['price']->getData());
            $variant->setProduct($entity);
            //$variant->setVslug(str_replace('--','-',URLify::filter($variant->getProduct()->getName())));

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->persist($variant);
            $em->flush();

            return $this->redirect($this->generateUrl('product_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Product entity.
     *
     * @Route("/new", name="product_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {

        $entity = new Product();
        $form   = $this->createForm(new ProductType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Product entity.
     *
     * @Route("/show/{id}/{locale}", name="product_show", defaults={"locale" = null})
     * @Method("GET")
     * @Template()
     */
    public function showAction($id, $locale = null)
    {
        $request = $this->getRequest();
        $rlocale = $request->getLocale();

        $loc = (null !== $locale) ? $locale : $rlocale;

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Product')->findByLocale($id, $loc);

        $variants = $entity->getVariants();


        /**
        $variants = $entity->getVariants();
        foreach($variants as $variant){
            foreach($variant->getValues() as $value){
                print_r($value->getOption()->getName());
            }
        }
         */



        //$entity->setName('mijn produkt slug in nl');
        //$entity->setTranslatableLocale('nl');
        //$em->persist($entity);
        //$em->flush();

        //exit();


        /**
        $repository = $em->getRepository('Gedmo\Translatable\Entity\Translation');
        $translations = $repository->findTranslations($entity);
        $trans = $em->getRepository('StoreProductBundle:ProductEntityTranslation')->findByForeignKey($id);
        if (!$trans) {
            echo 'no translations';
        } else {
            echo 'has translations';
            var_dump($trans);
        }
         */


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     * @Route("/{id}/edit", name="product_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Product')->findByLocale($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $editForm = $this->createForm(new ProductType(true), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     * @Route("/{id}/{locale}/edit", name="product_edit_translation")
     * @Method("GET")
     * @Template()
     */
    public function editTranslationAction($id, $locale)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Product')->findByLocale($id, $locale);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $editForm = $this->createForm(new ProductType(true), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'locale'    => $locale,
        );
    }


    /**
     * Edits an existing Product entity.
     *
     * @Route("/{id}/{locale}", name="product_update", defaults={"locale" = null})
     * @Method("PUT")
     * @Template("StoreProductBundle:Product:edit.html.twig")
     */
    public function updateAction(Request $request, $id, $locale = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        if (null !== $locale) {
            $entity->setTranslatableLocale($locale);
        } else {
            $entity->setTranslatableLocale('fr');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProductType(true), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            if (null !== $locale) {
                $entity->setSlug(URLify::filter($entity->getName()));
            }
            $entity->setFileUpdate(md5(time()));
            $em->persist($entity);
            $em->flush();

            if ($locale == 'fr_FR' || null == $locale) {
                //exit('yes');
                $x = $em->getRepository('StoreProductBundle:Product')->find($id);
                //$x->setTranslatableLocale('fr_FR');
                $x->setDescription($entity->getDescription());
                $x->setName($entity->getName());
                $em->persist($x);
                $em->flush();

                $entity->setTranslatableLocale('fr_FR');
                $em->persist($entity);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('product_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Product entity.
     *
     * @Route("/{id}", name="product_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('StoreProductBundle:Product')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Product entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('product'));
    }

    /**
     * Creates a form to delete a Product entity by id.
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

    /**
     * @Route("product/{id}/set/twoPlusOne", name="product_set_twoplusone")
     */
    public function twoPlusOneAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $p = $em->getRepository('StoreProductBundle:Product')->find($id);

        if (!$p) {
            throw new \InvalidArgumentException('Product not valid');
        }
        $status = $p->getTwoPlusOne();
        if (false == $status) {
            $p->setTwoPlusOne(true);
        } else {
            $p->setTwoPlusOne(false);
        }
        $em->persist($p);
        $em->flush();
        return $this->redirect($this->generateUrl('product'));
    }

}
