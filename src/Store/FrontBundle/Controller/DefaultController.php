<?php

namespace Store\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homeweb")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $man = $this->get('store.store_manager');

        $masterVariants = $man->getMasterVariants();

        $cart = $man->getCart();

        return array(
            'cart'  =>  $cart,
            'products'  =>  $masterVariants,
        );
    }

    /**
     * @Route("/product/{id}/{slug}", name="product_front_show", defaults={"slug"=null})
     * @Template()
     */
    public function showProductFrontAction($id, $slug)
    {
        $manager = $this->get('store.store_manager');

        $variant = $manager->getVariant($id);
        $product = $manager->getProductForVariant($variant->getProduct()->getId());

        if(!$product){
            throw new \InvalidArgumentException("The product does not exist");
        }

        return array(
            'variant'   => $variant,
            'product'   =>  $product
        );
    }

    /**
     * @Template()
     */
    public function showMiniCartAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('StoreProductBundle:Promotion');
        $promotions = $repo->findIfActual();
        $promotion = array();

        $storeManager = $this->get('store.store_manager');
        $cart = $storeManager->getCart();
        $total = 0;
        foreach ($cart->getItems() as $item) {
            $total = $total + ($item->getProduct()->getPrice() * $item->getQuantity());
        }

        if ($total !== 0){
        foreach($promotions as $pro){
            $promotion['detail'] = $pro;
            $promotion['discount_amount'] = (($total / 100) * $pro->getDiscount());
            $promotion['new_total'] = $total - $promotion['discount_amount'];
            break;
        }
        }

        return array(
            'cart'  =>  $cart,
            'total' => $total,
            'promotion' => $promotion,
        );
    }

    /**
     *
     * @Template()
     */
    public function getCategoryMenuAction()
    {
        $man = $this->get('store.store_manager');
        $categories = $man->getCategories(true);
        $htmlTree = $man->getMyTree();

        return array(
            'categories' => $categories,
            'htmlTree'  => $htmlTree,
        );
    }

    /**
     * @Route("/catalog/{id}/{slug}", name="category_page")
     * @Template()
     */
    public function categoryAction($id, $slug)
    {
        $man = $this->get('store.store_manager');
        $categories = $man->getCategories(true);
        $htmlTree = $man->getMyTree();

        $category = $man->getCategory($id);
        if (!$category){
            throw new \InvalidParameterException('The category does not exist');
        }
        $products = $man->getProductsForCategory($id);
        return array(
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
            'htmlTree' => $htmlTree,
        );
    }

    /**
     * @Route("/cart/view", name="front_cart")
     * @Template()
     */
    public function showCartAction()
    {
        $em = $this->getDoctrine()->getManager();
        $req = $this->getRequest();
        $em->getRepository('StoreProductBundle:Product')->findAllByLocaleForTrans($req->getLocale());
        $repo = $em->getRepository('StoreProductBundle:Promotion');
        $promotions = $repo->findIfActual();
        $promotion = array();

        $storeManager = $this->get('store.store_manager');


        $cart = $storeManager->getCart();

        $total = 0;
        foreach ($cart->getItems() as $item) {
            $total = $total + ($item->getProduct()->getPrice() * $item->getQuantity());
        }

        if ($total !== 0){
            foreach($promotions as $pro){
                $promotion['detail'] = $pro;
                $promotion['discount_amount'] = (($total / 100) * $pro->getDiscount());
                $promotion['new_total'] = $total - $promotion['discount_amount'];
                break;
            }
        }

        return array(
            'cart'  =>  $cart,
            'total' => $total,
            'promotion' => $promotion,
        );
    }

    /**
     * @Route("/add_item_to_cart/{productId}", name="add_item_to_cart")
     */
    public function addItemToCartAction($productId, Request $request)
    {
        $qty = 1;
        if ($request->getMethod() == 'POST') {
            $qty = $request->request->get('form_quantity');
        }
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('StoreProductBundle:Variant')->find($productId);
        if (!$product) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $man = $this->get('store.store_manager');
        $man->addItemToCart($product, $qty);

        $referer = $this->getRequest()->headers->get('referer');

        return $this->redirect($referer);
    }

    /**
     * @Route("/increment/item/{item}", name="increment_item")
     *
     */
    public function incrementItem($item)
    {
        $man = $this->get('store.store_manager');
        $man->incrementItem($item);

        $referer = $this->get('request')->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/decrement/item/{item}", name="decrement_item")
     */
    public function decrementItem($item)
    {
        $man = $this->get('store.store_manager');
        $man->incrementItem($item, true);

        $referer = $this->get('request')->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("reset_cart", name="reset_cart")
     */
    public function resetCart()
    {
        $man = $this->get('store.store_manager');
        $man->resetCart();

        return $this->redirect($this->generateUrl('homeweb'));
    }

    /**
     * @Template()
     */
    public function showCartLineItemAction($itemId, $quantity)
    {

        $manager = $this->get('store.store_manager');

        $em = $this->getDoctrine()->getManager();
        $pr = $em->getRepository('StoreProductBundle:Product');
        $r = $this->get('request');

        $variant = $manager->getVariant($itemId);
        $product = $pr->findSimpleByLocale($variant->getProduct()->getId(), $r->getLocale());

        if(!$product){
            throw new \InvalidArgumentException("The product does not exist");
        }

        return array(
            'item'   => $variant,
            'product'   =>  $product,
            'quantity'  => $quantity,
        );
    }

    /**
     * @Route("order/address", name="order_address")
     * @Template()
     */
    public function orderAddressAction()
    {
        $manager = $this->get('store.store_manager');
        $cart = $manager->getCart();


    }
}
