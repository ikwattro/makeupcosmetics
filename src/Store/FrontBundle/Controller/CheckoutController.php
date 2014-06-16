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
use Store\PaymentBundle\Entity\PaymentResult;
use Store\PaymentBundle\StatusResolver\OgoneStatusResolver;

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
            $em = $this->getDoctrine()->getManager();
            $cart = $man->getCart();
            if (!$cart->getCustomer()) {
                $cart->setCustomer($this->get('security.context')->getToken()->getUser());
                $em->persist($cart);
                $em->flush();
            }
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
            $promotion['disabled'] = $pro->getDisabled();
            break;
        }

        if (!empty($promotion)) {
            if ($promotion['new_total'] > 45) {
                $cart->setShippingPrice(0);
            }
        } elseif ($total > 45) {
            $free_shipping = true;
            $cart->setShippingPrice(0);
        }

        if (count($promotion) > 0) {
            $cart->setPromotionDiscount($promotion['discount_amount']);
            $em->persist($cart);
            $em->flush();
        }







        $twoPlusOneMap = array();
        foreach ($cart->getItems() as $item) {
            $v = $item->getProduct();
            $p = $v->getProduct();
            if ($p->getTwoPlusOne()) {
                if (array_key_exists($p->getId(), $twoPlusOneMap)) {
                    $twoPlusOneMap[$p->getId()] = $twoPlusOneMap[$p->getId()] + $item->getQuantity();
                } else {
                    $twoPlusOneMap[$p->getId()] = $item->getQuantity();
                }
            }

        }
        $twoPlusOneDiscountMap = 0;
        $r = $em->getRepository('StoreProductBundle:Product');

        foreach($twoPlusOneMap as $k => $q) {
            if ($q >= 2) {
                $pr = $r->find($k);
                $price = $pr->getPrice();
                $twoPlusOneDiscountMap = $twoPlusOneDiscountMap + (round($q/2, 0, PHP_ROUND_HALF_DOWN)*$price);
            }
        }

        $man->setCartConfirmStatus();
        $countries = Intl::getRegionBundle()->getCountryNames();

        $ogone = $this->buildOgoneForm($total, $cart, $promotion, $countries, $twoPlusOneDiscountMap);


        return array(
            'cart' => $cart,
            'countries' => $countries,
            'promotion' => $promotion,
            'total' => $total,
            'ogone' => $ogone,
            'ogoneMode' => $this->container->getParameter('ogone_mode'),
            'twoPlusOne' => $twoPlusOneDiscountMap,
        );
    }

    private function buildOgoneForm($total, $cart, $promotion, $countries, $twoPlusOneDiscountMap)
    {
        $locale = $this->get('request')->getLocale();
        $toLangs = array(
            'fr' => 'fr_FR',
            'nl' => 'nl_NL',
            'de' => 'de_DE',
            'en' => 'en_US'
        );
        $loc = explode('_', $locale);
        if(array_key_exists(strtolower($loc[0]), $toLangs)) {
            $lang = $toLangs[strtolower($loc[0])];
        } else {
            $lang = 'en_US';
        }
        $pspid = $this->container->getParameter('ogone_pspid');
        $orderId = $cart->getOrderId();

        $amount = $total;

        if (!empty($promotion)) {
                if ($promotion['disabled'] == false) {
                    $amount = $amount - $cart->getPromotionDiscount();
                }

        }

        if ($amount < 45) {
            $amount = $amount + $cart->getShippingMethod()->getPrice();
        }


        $amount = $amount - $twoPlusOneDiscountMap;



        $total = ($amount * 100 );
        $currency = 'EUR';
        $language = $lang;
        $email = $cart->getCustomer()->getEmail();
        $zipCode = $cart->getBillingAddress()->getZipCode();
        $address = $cart->getBillingAddress()->getLine1();
        $city = $cart->getBillingAddress()->getState();
        $country = $cart->getBillingAddress()->getCountry();

        $signature = $this->container->getParameter('ogone_sha1_in');

        $keys = array(
            'pspid' => $pspid,
            'orderId' => $orderId,
            'amount' => $total,
            'currency' => $currency,
            'language' => $language,
            //'email' => $email,
            //'zipCode' => $zipCode,
            //'address' => $address,
            //'town' => $city,
            //'cty' => $country
        );

        ksort($keys);

        $shaSign = '';
        foreach ($keys as $k => $v) {
            $shaSign .= strtoupper($k).'='.$v.$signature;
        }
        $hash = sha1($shaSign);
        $keys['shaSign'] = strtoupper($hash);


        return $keys;
    }

    /**
     * @Route("/checkout/order/payment/result", name="checkout_payment_result")
     * @Template()
     */
    public function checkoutOrderResultAction(Request $request)
    {
        $locale = $this->get('session')->get('lunetics_locale');
        $e = explode('_', $locale);
        $loc = $e[0];
        if (is_object($this->get('security.context')->getToken()->getUser())) {
            $customer_id = $this->get('security.context')->getToken()->getUser()->getId() ?: null;
        } else {
            $customer_id = null;
        }
        $outSig = $this->container->getParameter('ogone_sha1_out');
        $params = $request->query->all();

        $ogoneResolver = new OgoneStatusResolver($params['STATUS']);

        $result = new PaymentResult();
        $result->setDtg(new \DateTime("NOW"));
        $result->setUser($customer_id);
        $result->setOrderId($request->query->get('orderID'));
        $result->setPaymentPlatform('Ogone');
        $result->setResponseStatus($ogoneResolver->getStatusCode());
        $result->setResponseStatusHuman($ogoneResolver->getExplanation());
        $result->setBrand($request->query->get('BRAND'));
        $result->setIp($request->query->get('IP'));

        $em = $this->getDoctrine()->getManager();

        if ($ogoneResolver->isPaymentValid()) {
            $result->setPaymentValid(true);
            $cart = $this->get('store.store_manager')->getCart();
            $cart->setState('PAYMENT_COMPLETE');

            $em->persist($cart);
            $em->persist($result);
            $em->flush();

            $this->get('store.store_manager')->resetCart();

            $this->notifyAdmin(true, $cart->getOrderId());

            return array(
                'status' => $cart->getState(),
                'orderId' => $cart->getOrderId(),
                'locale' => $loc,
            );

        } else {
            $cart = $this->get('store.store_manager')->getCart();
            $cart->setState($ogoneResolver->getExplanation());
            $em->persist($cart);
            $result->setPaymentValid(false);
            $em->persist($result);
            $em->flush();

            $this->notifyAdmin(false, $cart->getOrderId());

            return $this->render(
                'StoreFrontBundle:Checkout:checkoutInvalid.html.twig',
                array('status' => $params['STATUS'], 'locale' => $loc)
            );
        }
    }

    /**
     * @Route("/checkout/payment/invalid", name="checkout_invalid")
     * @Template()
     */
    public function checkoutInvalidAction(array $params)
    {
        if (!$params['STATUS']) {
            throw new \InvalidArgumentException('Status Code Invalid');
        }

        $resolver = new OgoneStatusResolver($params['STATUS']);

        return array(
            'status' => $resolver->getExplanation()
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
        $total = 0;
        foreach ($cart->getItems() as $item) {
            $total = $total + ($item->getProduct()->getPrice() * $item->getQuantity());
        }
        if (null != $cart->getPromotionDiscount()) {
            $total = ($total - $cart->getPromotionDiscount());
        }
        if ($total > 45) {
            foreach ($available_methods as $m) {
                $m->setPrice(0);
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

    public function notifyAdmin($result, $orderId)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Notification Commande MakeUp Cosmetics')
            ->setFrom(array('makeupcosmetics.eu@gmail.com' => 'MakeUpCosmetics - System'))
            ->setTo(array('absoluttly@gmail.com', 'claudehaest@gmail.com'))
            ->setBody($this->renderView('StoreAdminBundle:Notification:orderResult.html.twig', array(
                'result' => $result,
                'orderId' => $orderId,
            )), 'text/html')
        ;
        $this->get('mailer')->send($message);
    }


}