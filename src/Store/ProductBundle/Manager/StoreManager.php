<?php

namespace Store\ProductBundle\Manager;

use Store\ProductBundle\Storage\SessionCartStorage;
use Doctrine\ORM\EntityManager;
use Store\ProductBundle\Entity\Cart;
use Store\ProductBundle\Entity\CartItem;
use Store\ProductBundle\Entity\Variant;
use Store\ProductBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

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

    private $cart;

    public function __construct(EntityManager $em, SessionCartStorage $storage, SessionInterface $session, Request $request)
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
        $p = $this->variant_repository->find($product->getId());

        if (!$p) {
            throw new \InvalidArgumentException('Product not found');
        }

        $cart = $this->getCart();

        $items = $cart->getItems();
        foreach ($items as $item) {
            if($item->getProduct() == $p) {
                $item->incrementQuantity($quantity);
                $this->em->persist($item);
                $this->em->flush();
                return true;
            }
        }

        $item = new CartItem($quantity);
        $item->setCart($cart);
        $item->setProduct($product);

        $this->cart->addItem($item);

        $this->em->persist($this->cart);
        $this->em->flush();

        $this->setMessage('Produit ajouté correctement au panier');

        return true;
    }

    private function setMessage($message)
    {
        $bag = $this->session->getFlashBag();
        $bag->add('notice', $message);
    }

    public function countCartItems()
    {
        $cart = $this->getCart();

        return count($cart->getItems());
    }

    public function resetCart()
    {
        $this->cart = null;
        $this->storage->resetCurrentCartIdentifier();
        $this->setMessage('Votre panier a été réinitialisé');

        return true;
    }

    public function getMasterVariants()
    {
        return $this->product_repository->findAllByLocale($this->locale);
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



}