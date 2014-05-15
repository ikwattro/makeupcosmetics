<?php

namespace Store\FrontBundle\Controller;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Store\AddressBundle\Entity\Address;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Intl\Intl;

class CheckoutController extends Controller
{
    /**
     * @Route("/checkout/step/account", name="checkout_account")
     * @Template()
     */
    public function checkoutAccountAction()
    {
        if($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $man = $this->get('store.store_manager');
            $man->setCartAuthStatus();
            return $this->redirect($this->generateUrl('checkout_address'));
        }

        return array();
    }

    /**
     * @Route("/checkout/step/address", name="checkout_address")
     * @Template()
     */
    public function checkoutAddressAction(Request $request)
    {
        if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('checkout_account'));
        }
        $man = $this->get('store.store_manager');
        $man->setCartAddressStatus();

        if($request->isMethod('POST')){
            $formData = $request->request->get('form');
            if (isset($formData['both'])) {
                $newForm = array(
                    'firstname' => $formData['firstname'],
                    'lastname'  => $formData['lastname'],
                    'address_line_1' => $formData['address_line_1'],
                    'address_line_2' => $formData['address_line_2'],
                    'zip_code' => $formData['zip_code'],
                    'city' => $formData['city'],
                    'country' => $formData['country'],
                    'both' => $formData['both'],
                    '_token' => $formData['_token']
                );
                $request->request->replace(array('form' => $newForm));
        }}
        $dat = $request->request->get('form');
        if(isset($dat['both']) && $dat['both'] == 1) {
            var_dump($dat['both']);
            $billing_form = $this->createMiniBillingAddressForm();
        } else {
            $billing_form = $this->createBillingAddressForm();
        }

        $billing_form->handleRequest($request);

        if ($billing_form->isValid()) {
            $data = $billing_form->getData();
            $this->createAddress($data);



            return $this->redirect($this->generateUrl('checkout_shipping'));
        }
        $cart = $man->getCart();
        $addrSet = false;
        if ($cart->getBillingAddress() && $cart->getShippingAddress()) {
            $addrSet = true;
        }
        var_dump($addrSet);

        return array('form' => $billing_form->createView(), 'isSet' => $addrSet);
    }

    /**
     * @Route("/checkout/step/confirmation", name="checkout_confirm")
     * @Template()
     */
    public function checkoutConfirmAction()
    {
        $em = $this->getDoctrine()->getManager();
        $req = $this->getRequest();
        $em->getRepository('StoreProductBundle:Product')->findAllByLocaleForTrans($req->getLocale());
        $man = $this->get('store.store_manager');
        $cart = $man->getCart();
        if (count($cart->getItems()) <= 0) {
            return $this->redirect($this->generateUrl('homeweb'));
        }
        if (!$cart->getBillingAddress() || !$cart->getShippingAddress()) {
            return $this->redirect($this->generateUrl('checkout_shipping'));
        }

        $repo = $em->getRepository('StoreProductBundle:Promotion');
        $promotions = $repo->findIfActual();
        $promotion = array();
        $total = 0;
        foreach ($cart->getItems() as $item) {
            $total = $total + ($item->getProduct()->getPrice() * $item->getQuantity());
        }
        foreach($promotions as $pro){
            $promotion['detail'] = $pro;
            $promotion['discount_amount'] = (($total / 100) * $pro->getDiscount());
            $promotion['new_total'] = $total - $promotion['discount_amount'];
            break;
        }

        $man->setCartConfirmStatus();
        $countries = Intl::getRegionBundle()->getCountryNames();
        return array(
            'cart' => $cart,
            'countries' => $countries,
            'promotion' => $promotion,
            'total' => $total
        );
    }

    /**
     * @Route("/checkout/step/shipping", name="checkout_shipping")
     * @Template()
     */
    public function checkoutShippingAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            if ($this->verifyAndSaveShippingMethod($request)) {
                return $this->redirect($this->generateUrl('checkout_confirm'));
            }
        }

        $man = $this->get('store.store_manager');
        $man->setCartShippingStatus();
        $cart = $man->getCart();
        $country = $cart->getShippingAddress()->getCountry();
        $em = $this->getDoctrine()->getManager();
        $available_methods = array();
        $shippingMethods = $em->getRepository('StoreShippingBundle:ShippingMethod')->findAll();
        foreach ($shippingMethods as $method) {
            if (in_array($country, $method->getZone()->getCountries())) {
                $available_methods[] = $method;
            }
        }
        return array(
            'methods' => $available_methods
        );
    }

    private function verifyAndSaveShippingMethod(Request $request)
    {
        $man = $this->get('store.store_manager');
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        if (isset($data['form_shipping_method'])) {
            $method = $em->getRepository('StoreShippingBundle:ShippingMethod')->find($data['form_shipping_method']);
            if (!$method) {
                throw new InvalidArgumentException('Shipping Method Not Found');
            }
            $cart = $man->getCart();
            $cart->setShippingMethod($method);
            $cart->setShippingPrice($method->getPrice());
            $em->persist($cart);
            $em->flush();

            return true;
        } else { return false; }

    }

    /**
     * @param array $data
     * @throws \InvalidArgumentException
     */

    public function createAddress(array $data = array())
    {
        if (!$data) {
            throw new \InvalidArgumentException('No Form Data');
        }
        $cart = $this->get('store.store_manager')->getCart();
        $customer = $this->get('store.customer_manager')->getCustomer();
        $address = new Address();
        $address->setFirstname($data['firstname']);
        $address->setLastname($data['lastname']);
        $address->setCountry($data['country']);
        $address->setLine1($data['address_line_1']);
        $address->setLine2($data['address_line_2']);
        $address->setZipCode($data['zip_code']);
        $address->setState($data['city']);
        $address->setCustomer($customer);


        $em = $this->getDoctrine()->getManager();
        $customer->addAddresse($address);
        $cart->setBillingAddress($address);
        if($data['both'] == 1) {
            $address->setIsBothType();
            $cart->setShippingAddress($address);
        } else {
            $address->setIsBillingType();
            $address2 = new Address();
            $address2->setFirstname($data['firstname_2']);
            $address2->setLastname($data['lastname_2']);
            $address2->setCountry($data['country_2']);
            $address2->setLine1($data['address_line_1_2']);
            $address2->setLine2($data['address_line_2_2']);
            $address2->setZipCode($data['zip_code_2']);
            $address2->setState($data['city_2']);
            $address2->setCustomer($customer);
            $address2->setIsShippingType();
            $cart->setShippingAddress($address2);
            $customer->addAddresse($address2);
        }

        $em->persist($customer);
        $em->persist($cart);
        $em->flush();

    }

    private function createBillingAddressForm($defaultData = array())
    {
        $defaultData = $this->getDefaultAddressData();

        $form = $this->createFormBuilder($defaultData)
            ->add('firstname', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2))
                )
            ))
            ->add('lastname', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2))
                )
            ))
            ->add('address_line_1', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 5))
                )
            ))
            ->add('address_line_2', 'text')
            ->add('zip_code', 'text', array(
                'constraints' => array(
                    new Length(array('min' => 3)),
                    new NotBlank(),
                )
            ))
            ->add('city', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2))
                )
            ))
            ->add('country', 'country', array(
                'constraints' => array(
                    new Country(),
                )
            ))
            ->add('firstname_2', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2))
                )
            ))
            ->add('lastname_2', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2))
                )
            ))
            ->add('address_line_1_2', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 5))
                )
            ))
            ->add('address_line_2_2', 'text')
            ->add('zip_code_2', 'text', array(
                'constraints' => array(
                    new Length(array('min' => 3)),
                    new NotBlank(),
                )
            ))
            ->add('city_2', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2))
                )
            ))
            ->add('country_2', 'country', array(
                'constraints' => array(
                    new Country(),
                )
            ))
            ->add('both', 'checkbox')
            ->add('submit', 'submit')
            ->getForm();

        return $form;
    }

    private function createMiniBillingAddressForm($defaultData = array())
    {
        $form = $this->createFormBuilder($defaultData)
            ->add('firstname', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2))
                )
            ))
            ->add('lastname', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2))
                )
            ))
            ->add('address_line_1', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 5))
                )
            ))
            ->add('address_line_2', 'text')
            ->add('zip_code', 'text', array(
                'constraints' => array(
                    new Length(array('min' => 3)),
                    new NotBlank(),
                )
            ))
            ->add('city', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2))
                )
            ))
            ->add('country', 'country', array(
                'constraints' => array(
                    new Country(),
                )
            ))
            ->add('both', 'checkbox')
            ->add('submit', 'submit')
            ->getForm();

        return $form;
    }

    private function getDefaultAddressData()
    {
        $store = $this->get('store.store_manager');
        $cart = $store->getCart();
        if ($cart->getBillingAddress() && $cart->getShippingAddress()) {
            if ($cart->getBillingAddress() == $cart->getShippingAddress()) {
                $address = $cart->getBillingAddress();
                return array(
                    'firstname' =>  $address->getFirstName(),
                    'lastname'  =>  $address->getLastName(),
                    'address_line_1'    =>  $address->getLine1(),
                    'address_line_2'    =>  $address->getLine2(),
                    'zip_code'  =>  $address->getZipCode(),
                    'city'  =>  $address->getState(),
                    'country' => $address->getCountry(),
                    'both'  => true,
                );
            } else {
                $address = $cart->getBillingAddress();
                $address2 = $cart->getShippingAddress();
                return array(
                    'firstname' =>  $address->getFirstName(),
                    'lastname'  =>  $address->getLastName(),
                    'address_line_1'    =>  $address->getLine1(),
                    'address_line_2'    =>  $address->getLine2(),
                    'zip_code'  =>  $address->getZipCode(),
                    'city'  =>  $address->getState(),
                    'country' => $address->getCountry(),
                    'both'  => false,
                    'firstname_2' =>  $address2->getFirstName(),
                    'lastname_2'  =>  $address2->getLastName(),
                    'address_line_1_2'    =>  $address2->getLine1(),
                    'address_line_2_2'    =>  $address2->getLine2(),
                    'zip_code_2'  =>  $address2->getZipCode(),
                    'city_2'  =>  $address2->getState(),
                    'country_2' => $address2->getCountry(),
                );
            }

        }
        return array();
    }

    /**
     * @param Request $request
     * @return mixed
     * @Template()
     */
    public function loginAction(Request $request)
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        $csrfToken = $this->container->has('form.csrf_provider')
            ? $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate')
            : null;

        return array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'csrf_token' => $csrfToken,
        );
    }


}