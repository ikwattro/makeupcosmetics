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

        $masterVariants = $man->getMasterVariants(null, 20);

        $cart = $man->getCart();

        return array(
            'cart'  =>  $cart,
            'products'  =>  $masterVariants,
        );
    }

    /**
     * @Route("catalog/product/{id}/{slug}", name="product_front_show", defaults={"slug"=null})
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
     * @Template()
     */
    public function getCategoryMenuAction()
    {
        $man = $this->get('store.store_manager');
        $parcours = $man->getCategoriesWithChildren();
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

        $free_shipping = false;

        if (!empty($promotion)) {
            if ($promotion['new_total'] > 45) {
                $free_shipping = true;
            }
        } elseif ($total > 45) {
            $free_shipping = true;
        }

        return array(
            'cart'  =>  $cart,
            'total' => $total,
            'promotion' => $promotion,
            'free_shipping' => $free_shipping,
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

    /**
     * @Route("/cart/item/remove/{itemId}", name="remove_cart_item")
     */
    public function removeCartItemAction($itemId)
    {
        $man = $this->get('store.store_manager');
        $referer = $this->get('request')->headers->get('referer');
        if ($man->removeItem($itemId)) {
            $man->setMessage('Le produit a été retiré du panier');
            return $this->redirect($referer);
        }
        $man->setMessage('Impossible de retirer le produit du panier');
        return $this->redirect($referer);

    }

    /**
     * @Route("/email1234567890RYRYRYRYRYRYRY/", name="send_email")
     * @Template()
     */
    public function sendEmailAction()
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom(array('info@makeup-cosmetics.eu'=> 'MakeupCosmetics.eu'))
            ->setTo(array('claudehaest@skynet.be' => 'Claude Haest'))
            ->setSender('support@makeup-cosmetics.eu')
            ->setReplyTo('absoluttly@gmail.com')
            ->setBody(
                $this->renderView('StoreFrontBundle:Default:email.txt.twig')
            );

        $this->get('mailer')->send($message);

        return array();
    }

    /**
     * @Template()
     */
    public function showFrontCartAction()
    {
        $cart = $this->get('store.store_manager')->getCart();
        $items = $cart->getItems();
        $c = count($items);

        return array(
            'cart' => $cart,
            'items' => $c,
        );
    }

    /**
     * @Route("/conditions-generales-de-vente", name="page_cgv")
     * @Template()
     */
    public function cgvAction()
    {
        return array();
    }

    /**
     * @Route("/mentions-legales", name="page_mentions")
     * @Template()
     */
    public function mentionsAction()
    {
        return array();
    }

    /**
     * @Route("/shipping-info", name="page_shipping")
     * @Template()
     */
    public function shippingInfoAction()
    {
        return array();
    }

    /**
     * @Route("/sitemap.{_format}", name="front_sitemaps_sitemap", Requirements={"_format" = "xml"})
     * @Template("StoreCoreBundle:Default:sitemap.xml.twig")
     */
    public function sitemapAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $urls = array();
        $hostname = $this->getRequest()->getHost();

        // add some urls homepage
        $urls[] = array('loc' => $this->get('router')->generate('homeweb'), 'changefreq' => 'daily', 'priority' => '0.9');

        $language = array();

        $mv = $em->getRepository('StoreProductBundle:Variant')->findAll();

        foreach($mv as $p) {

            if ($p->getProduct()->getAvailable() !== false && $p->getOutOfStock() == false) {
                $r = $this->get('router')->generate('product_front_show', array('id' => $p->getId(), 'slug' => $p->getProduct()->getSlug()));
                $urls[] = array('loc' => $r, 'changefreq' => 'weekly', 'priority' => '0.9');
            }


        }

        /**
        // multi-lang pages
        foreach($languages as $lang) {
        $urls[] = array('loc' => $this->get('router')->generate('home_contact', array('_locale' => $lang)), 'changefreq' => 'monthly', 'priority' => '0.3');
        }
         **/
        /**
        // urls from database
        $urls[] = array('loc' => $this->get('router')->generate('home_product_overview', array('_locale' => 'en')), 'changefreq' => 'weekly', 'priority' => '0.7');
        // service
        foreach ($em->getRepository('AcmeSampleStoreBundle:Product')->findAll() as $product) {
        $urls[] = array('loc' => $this->get('router')->generate('home_product_detail',
        array('productSlug' => $product->getSlug())), 'priority' => '0.5');
        }
         **/
        return array('urls' => $urls, 'hostname' => $hostname);
    }

}
