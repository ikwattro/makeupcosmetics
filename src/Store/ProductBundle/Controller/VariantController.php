<?php

namespace Store\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Store\ProductBundle\Entity\Variant;
use Store\ProductBundle\Form\VariantType;

/**
 * Variant controller.
 *
 * @Route("/variant")
 */
class VariantController extends Controller
{

    /**
     * Lists all Variant entities.
     *
     * @Route("/", name="variant")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('StoreProductBundle:Variant')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Variant entity.
     *
     * @Route("/create/{productId}", name="variant_create")
     * @Method("POST")
     * @Template("StoreProductBundle:Variant:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Variant();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $options = $entity->getProduct()->getOptions();
            $em = $this->getDoctrine()->getManager();
            $vslug = '';
            $productName = $entity->getProduct()->getName();
            $vslug .= $vslug.$productName ;
            foreach ($entity->getValues() as $val) {
                $vslug .= $vslug.$val->getName();
            }
            $entity->setVslug($vslug);


            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('variant_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/new_variant_for_product/{productId}", name="new_variant_for_product")
     * @Template()
     */
    public function newVariantForProductAction($productId)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('StoreProductBundle:Product')->find($productId);

        if (!$product) {
            throw new \InvalidArgumentException('Product not found');
        }

        $options = $product->getOptions();

        return array(
            'product' => $product,
            'options' => $options,
        );
    }

    /**
     * @Route("/edit_variant/{id}", name="edit_variant")
     * @Template()
     */
    public function editVariantAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $variant = $em->getRepository('StoreProductBundle:Variant')->find($id);

        $options = $variant->getProduct()->getOptions();

        if (!$variant) {
            throw new \InvalidArgumentException('Variant not found');
        }

        return array(
            'variant' => $variant,
            'options' => $options,
            'price' => $variant->getPrice(),
            'out_of_stock' => $variant->getOutOfStock(),
            'isPromo' => $variant->getIsPromo(),
            'promoPrice' => $variant->getPromoPrice(),
        );
    }

    /**
     * @Route("/update_variant/{id}", name="update_variant")
     * @Method("POST")
     */
    public function updateVariantAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $variant = $em->getRepository('StoreProductBundle:Variant')->find($id);

        $request = $this->getRequest();
        $form = $request->request->get('variant_for_product');

        $variant->setPrice($form['price']);
        if(isset($form['out_of_stock'])){
            $variant->setOutOfStock($form['out_of_stock']);
        } else {
            $variant->setOutOfStock(0);
        }
        foreach ($variant->getValues() as $value) {
            $variant->removeValue($value);
        }

        if (isset($form['option'])) {
        foreach ($form['option'] as $key => $val) {
            $optionValue = $em->getRepository('StoreProductBundle:OptionValue')->find($val);
            $variant->addValue($optionValue);
        }
        }

        if(isset($form['isPromo'])) {
            $variant->setIsPromo(true);
            $variant->setPromoPrice($form['promoPrice']);
        } else {
            $variant->setIsPromo(false);
            $variant->setPromoPrice(null);
        }

        $productName = $variant->getProduct()->getName();
        $vslug = '';
        foreach ($variant->getValues() as $val) {
            $vslug .= $vslug.$val->getName();

        }
        $sl = $vslug;
        $vslug = \URLify::filter($sl);
        $variant->setVslug($this->removedTirets($vslug));

        $em->persist($variant);
        $em->flush();

        return $this->redirect($this->generateUrl('product_show', array('id' => $variant->getProduct()->getId())));

    }

    /**
     * @Route("/create_for_product/{productId}", name="create_for_product")
     * @Method("POST")
     * @Template()
     */
    public function createForProductAction($productId)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('StoreProductBundle:Product')->find($productId);

        if (!$product) {
            throw new \InvalidArgumentException('Product not found');
        }

        $variant = new Variant();
        $variant->setProduct($product);

        $request = $this->getRequest();
        $form = $request->request->get('variant_for_product');

        foreach ($form['option'] as $key => $val) {
            //$optionType = $em->getRepository('StoreProductBundle:OptionType')->find($key);
            $optionValue = $em->getRepository('StoreProductBundle:OptionValue')->find($val);

            $variant->addValue($optionValue);
        }

        if(null === $form['price']) {
            $price = $product->getPrice();
        } else {
            $price = $form['price'];
        }
        $variant->setPrice($price);
        $variant->setIsMaster(false);
        $productName = $product->getName();
        $vslug = '';
        foreach ($variant->getValues() as $val) {
            $vslug .= $vslug.$val->getName();

        }
        $sl = $vslug;
        $vslug = \URLify::filter($sl);
        $variant->setVslug($this->removedTirets($vslug));

        $em->persist($variant);
        $em->flush();

        return $this->redirect($this->generateUrl('product_show', array('id' => $product->getId())));



    }

    /**
    * Creates a form to create a Variant entity.
    *
    * @param Variant $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Variant $entity, $options)
    {

        $form = $this->createForm(new VariantType($options, $entity), $entity, array(
            'action' => $this->generateUrl('variant_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Variant entity.
     *
     * @Route("/new/{productId}", name="variant_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($productId)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('StoreProductBundle:Product')->find($productId);

        if (!$product) {
            throw new \InvalidArgumentException('Product not found');
        }
        $entity = new Variant();
        $entity->setPrice($product->getMasterVariant()->getPrice());
        $entity->setProduct($product);
        $options = $product->getOptions();

        $form   = $this->createCreateForm($entity, $options);



        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Variant entity.
     *
     * @Route("/{id}", name="variant_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Variant')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Variant entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Variant entity.
     *
     * @Route("/{id}/edit", name="variant_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Variant')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Variant entity.');
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
    * Creates a form to edit a Variant entity.
    *
    * @param Variant $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Variant $entity)
    {
        $product = $entity->getProduct();
        $options = $product->getOptions();
        foreach($entity->getValues() as $value){
            $entity->addVal($value, $value->getOption()->getName());
        }
        $form = $this->createForm(new VariantType($options, $entity), $entity, array(
            'action' => $this->generateUrl('variant_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Variant entity.
     *
     * @Route("/{id}", name="variant_update")
     * @Method("PUT")
     * @Template("StoreProductBundle:Variant:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('StoreProductBundle:Variant')->find($id);
        $optionValues = $entity->getValues();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Variant entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $options = $entity->getProduct()->getOptions();
            $values = $entity->getValues();
            foreach($values as $val) {
                $entity->removeValue($val);
            }
            foreach($options as $option){
                $entity->addValue($editForm[$option->getName()]->getData());
            }
            $productName = $entity->getProduct()->getName();
            $vslug = '';
            foreach ($entity->getValues() as $val) {
                $vslug .= $vslug.$val->getName();

            }
            $sl = $vslug;
            $vslug = URLify::filter($sl);
            $entity->setVslug($this->removedTirets($vslug));
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('product_show', array('id' => $entity->getProduct()->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Variant entity.
     *
     * @Route("/{id}", name="variant_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('StoreProductBundle:Variant')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Variant entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('variant'));
    }

    /**
     * Creates a form to delete a Variant entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('variant_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    private function removedTirets($slug)
    {
        return str_replace('--', '-', $slug);
    }

    /**
     * @Route("/soft/regenerateslugs", name="variant_slug_regen")
     * @Template()
     */
    public function regenerateVariantSlugsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $variants = $em->getRepository('StoreProductBundle:Variant')->findAll();
        $done = array();

        foreach ($variants as $variant) {
            $slug = '';
            foreach ($variant->getValues() as $val) {
                $slug .= $val->getName();
            }
            $vslug = $this->removedTirets($slug);
            $variant->setVslug(\URLify::filter($vslug));
            $em->persist($variant);
            $em->flush();
            $done[] = array(
                'product' => $variant->getProduct()->getName(),
                'vslug' => \URLify::filter($vslug),
            );
        }
        return array(
            'done' => $done,
        );
    }
}
