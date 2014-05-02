<?php

namespace Store\ProductBundle\Storage;

use Store\ProductBundle\Entity\Cart;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Stores current cart id inside the user session.
 *
 */
class SessionCartStorage
{
    const KEY = '_store.cart-id';

    /**
     * Session.
     *
     * @var SessionInterface
     */
    protected $session;

    /**
     * Key to store the cart id in session.
     *
     * @var string
     */
    protected $key;

    /**
     * Constructor.
     *
     * @param SessionInterface $session
     * @param string           $key
     */
    public function __construct(SessionInterface $session, $key = self::KEY)
    {
        $this->session = $session;
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCartIdentifier()
    {
        return $this->session->get($this->key);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentCartIdentifier(Cart $cart)
    {
        $this->session->set($this->key, $cart->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function resetCurrentCartIdentifier()
    {
        $this->session->remove($this->key);
    }
}