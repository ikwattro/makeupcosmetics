<?php

namespace Store\ProductBundle\Manager;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Store\ProductBundle\Storage\SessionCartStorage;
use Doctrine\ORM\EntityManager;
use Store\ProductBundle\Entity\Cart;
use Store\ProductBundle\Entity\CartItem;
use Store\ProductBundle\Entity\Variant;
use Store\ProductBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class StoreManager
{

    private $storage;
    private $em;
    private $cart_repository;
    private $cart_item_repository;
    private $product_repository;
    private $variant_repository;
    private $category_repository;
    private $session;
    private $locale;
    private $security_context;
    private $userAgent;
    private $translator;

    private $cart;

    public function __construct(EntityManager $em, SessionCartStorage $storage, SessionInterface $session, Request $request, SecurityContextInterface $security_context, $translator)
    {
        $this->em = $em;
        $this->storage = $storage;
        $this->cart_repository = $em->getRepository('StoreProductBundle:Cart');
        $this->cart_item_repository = $em->getRepository('StoreProductBundle:CartItem');
        $this->product_repository = $em->getRepository('StoreProductBundle:Product');
        $this->variant_repository = $em->getRepository('StoreProductBundle:Variant');
        $this->category_repository = $em->getRepository('StoreProductBundle:Category');
        $this->session = $session;
        $this->locale = $request->getLocale();
        $this->security_context = $security_context;
        $this->userAgent = $request->server->get('HTTP_USER_AGENT');
        $this->translator = $translator;
    }

    public function getProductRepository()
    {
        return $this->product_repository;
    }

    public function getCartRepository()
    {
        return $this->cart_repository;
    }

    public function hasCart()
    {
        return null !== $this->cart;
    }

    public function getCart()
    {
        if ($this->isBotdetected()) {
            //return;
        }

        if (null !== $this->cart) {
            return $this->cart;
        }

        $cartIdentifier = $this->storage->getCurrentCartIdentifier();

        if ($cartIdentifier && $cart = $this->getCartByIdentifier($cartIdentifier)) {
            return $this->cart = $cart;
        }

        $this->cart = $this->createNewCart();
        $this->storage->setCurrentCartIdentifier($this->cart);

        return $this->cart;
    }

    public function createNewCart()
    {
        $cart = new Cart();
        $cart->setSessionId($this->session->getId());
        $cart->setCartDtg(new \DateTime("NOW"));
        $cart->setUserAgent($this->userAgent);
        if ($this->isBotdetected()) {
            $cart->setIsBot(true);
        }
        $cart->setState('CART');
        $cart->setPromotionDiscount(0);
        if($this->isAuth()) {
            $cart->setCustomer($this->security_context->getToken()->getUser());
        }
        $this->em->persist($cart);
        $this->em->flush();

        $cart->setOrderId(date('Ymd').$cart->getId());
        $this->em->persist($cart);
        $this->em->flush();

        return $cart;
    }

    protected function getCartByIdentifier($identifier)
    {
        return $this->cart_repository->find($identifier);
    }

    public function addItemToCart(Variant $product, $quantity = 1)
    {
        $this->refreshIfLoggedIn();

        $p = $this->variant_repository->find($product->getId());

        if (!$p) {
            throw new \InvalidArgumentException('Product not found');
        }

        $cart = $this->getCart();
        $itemsTotal = $cart->getItemsTotal() != null ? $cart->getItemsTotal() : 0;

        $items = $cart->getItems();
        foreach ($items as $item) {
            if($item->getProduct() == $p) {
                $item->incrementQuantity($quantity);
                $cart->setItemsTotal($itemsTotal + $quantity);
                $this->em->persist($item);
                $this->em->persist($cart);
                $this->em->flush();
                return true;
            }
        }

        $item = new CartItem($quantity);
        $item->setCart($cart);
        $item->setProduct($product);

        $this->cart->addItem($item);
        $this->cart->setItemsTotal($itemsTotal + $quantity);

        $this->em->persist($this->cart);
        $this->em->flush();

        $this->setMessage(ucfirst($this->translator->trans('cart.product_added', array(), 'Interface')));

        return true;
    }

    public function incrementItem($item, $inverse = false)
    {
        $item = $this->cart_item_repository->find($item);
        if (!$item) {
            throw new InvalidArgumentException('Item not found');
        }
        $items = $this->getCart()->getItems();
        if (!$items->contains($item)) {
            throw new InvalidArgumentException('Item not in Cart');
        }
        $addition = true === $inverse ? -1 : +1;
        foreach ($items as $it) {
            if ($it->getId() == $item->getId()) {
                if ($it->getQuantity() == 1) {
                    return true;
                }
                $nq = $it->getQuantity() + $addition ;
                $it->setQuantity($nq);
            }
        }

        $cart = $this->getCart();
        $itemsTotal = $cart->getItemsTotal() != null ? $cart->getItemsTotal() : 0;
        $cart->setItemsTotal($itemsTotal + $addition);
        $this->em->persist($cart);
        $this->em->flush();

        return $item;

    }

    public function setMessage($message)
    {
        $bag = $this->session->getFlashBag();
        $bag->add('notice', $message);
    }

    public function countCartItems()
    {
        $cart = $this->getCart();

        return count($cart->getItems());
    }

    public function resetCart($afterPayment = false)
    {
        $this->cart = null;
        $this->storage->resetCurrentCartIdentifier();
        if (!$afterPayment) {
            $this->setMessage(ucfirst($this->translator->trans('cart.reset_complete', array(), 'Interface')));
        }


        return true;
    }

    public function getMasterVariants($locale = null, $limit = null)
    {
        return $this->product_repository->findAllByLocale($this->locale, $limit);
    }

    public function getVariant($variantId)
    {
        return $this->variant_repository->find($variantId);
    }

    public function getProductForVariant($productId)
    {
        return $this->product_repository->findByLocale($productId, $this->locale);
    }

    public function getCategories($onlyRoots = false, $arrayze = false)
    {
        return $this->category_repository->findAllByLocale($this->locale, $onlyRoots, $to_array = $arrayze);
    }

    public function getCategoriesWithChildren()
    {
        return $this->category_repository->findAllByLocaleWithChildren($this->locale);
    }

    public function getCategory($id)
    {
        return $this->category_repository->findByLocale($id, $this->locale);
    }

    public function getCategoryTree()
    {
        $htmlTree = $this->category_repository->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* true: load all children, false: only direct */
            array(
                'decorate' => true,
                'representationField' => 'slug',
                'html' => true
            )
        );
        return $htmlTree;
    }

    public function getMyTree()
    {
        $options = array('decorate' => true);
        $cats = $this->getCategories(false, true);
        $tree = $this->category_repository->buildTree($cats, $options);

        return $tree;
    }

    public function getProductsForCategory($id)
    {
        return $this->product_repository->findAllByCategory($id, $this->locale);
    }

    public function isAuth()
    {
        if($this->security_context->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return true;
        }
        return false;
    }

    public function getCustomer()
    {
        if($this->security_context->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->security_context->getToken()->getUser();
        }
        return false;
    }

    public function setCustomerForCart($andFlush = false)
    {
        if($this->isAuth()) {
            $cart = $this->getCart();
            $cart->setCustomer($this->getCustomer());
            if($andFlush){
                $this->em->persist($cart);
                $this->em->flush();
                return $cart;
            }

        }
        return $this->getCart();
    }

    private function refreshIfLoggedIn()
    {
        if($this->isAuth() && $this->getCart()->getCustomer() == null) {
            $this->setCustomerForCart(true);
        }
    }

    public function setCartAuthStatus()
    {
        $cart = $this->getCart();
        $status = 'AUTH';
        $cart->setProcessStatus($status);
        $this->em->persist($cart);
        $this->em->flush();
    }

    public function setCartAddressStatus()
    {
        $cart = $this->getCart();
        $status = 'ADDR';
        $cart->setProcessStatus($status);
        $this->em->persist($cart);
        $this->em->flush();
    }

    public function setCartConfirmStatus()
    {
        $cart = $this->getCart();
        $status = 'CONF';
        $cart->setProcessStatus($status);
        $this->em->persist($cart);
        $this->em->flush();
    }

    public function setCartShippingStatus()
    {
        $cart = $this->getCart();
        $status = 'SHIPPING';
        $cart->setProcessStatus($status);
        $this->em->persist($cart);
        $this->em->flush();

    }

    public function removeItem($item)
    {
        $em = $this->em;
        $it = $this->cart_item_repository->find($item);
        if (!$it) {
            throw new \InvalidArgumentException('Item not found');
        }
        $cart = $this->getCart();
        $cart->setItemsTotal($cart->getItemsTotal() - $it->getQuantity());
        $cart->removeItem($it);
        $em->remove($it);
        $em->flush();
        $this->cart = $cart;
        return $this->cart;
    }

    public function isBotdetected()
    {

        if (preg_match('/bot|crawl|slurp|spider|facebook/i', $this->userAgent)) {
            return true;
        }
        else {
            return false;
        }

    }

    public function getOrdersForUser($user)
    {
        $repo = $this->cart_repository;

        return $repo->findAllForUser($user->getId());
    }

    private function getCartForId($id)
    {
        $cart = $this->cart_repository->find($id);
        if (!$cart) {
            throw new \InvalidArgumentException('Bad Cart Id');
        }
        return $cart;

    }

    public function setOrderProcessStatus($id, $status)
    {
        $cart = $this->getCartForId($id);

        $cart->setOrderProcessStatus($status);
        $this->em->persist($cart);
        $this->em->flush();

        return $cart;


    }

    public function setOrderReceived($id)
    {
        $cart = $this->getCartForId($id);


        $cart->setOrderProcessStatus('RECEIVED');
        $this->em->persist($cart);
        $this->em->flush();
    }

    public function setOrderReady($id)
    {
        $cart = $this->getCartForId($id);


        $cart->setOrderProcessStatus('READY');
        $this->em->persist($cart);
        $this->em->flsuh();
    }

    public function setOrderShipped($id)
    {
        $cart = $this->getCartForId($id);


        $cart->setOrderProcessStatus('SHIPPED');
        $this->em->persist($cart);
        $this->em->flsuh();
    }

    public function setOrderTerminated($id)
    {
        $cart = $this->getCartForId($id);


        $cart->setOrderProcessStatus('TERMINATED');
        $this->em->persist($cart);
        $this->em->flsuh();
    }

    public function setOrderError($id)
    {
        $cart = $this->getCartForId($id);


        $cart->setOrderProcessStatus('ERROR');
        $this->em->persist($cart);
        $this->em->flsuh();
    }

    public function isActivePromo()
    {
        $promos = $this->em->getRepository('StoreProductBundle:Promotion')->findAll();
        $now = new \DateTime("NOW");
        foreach ($promos as $promo) {
            if ($promo->getDisabled() !== true) {
                if ($promo->getStart() <= $now && $promo->getEnd() > $now) {
                    return $promo;
                }
            }
        }
        return false;
    }



}