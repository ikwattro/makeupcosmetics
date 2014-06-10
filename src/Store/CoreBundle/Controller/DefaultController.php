<?php

namespace Store\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="admin_home")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('StoreProductBundle:Variant')->findAll();

        return array(
            'numberOfProducts'  =>  count($products),
        );
    }

    /**
     * @Route("/customers", name="admin_customers")
     * @Template()
     */
    public function customersAction()
    {
        $em = $this->getDoctrine()->getManager();

        $customers = $em->getRepository('StoreCustomerBundle:Customer')->findAll();

        return array(
            'customers' => $customers
        );
    }

    /**
     * @Route("/sitemap.{_format}", name="sample_sitemaps_sitemap", Requirements={"_format" = "xml"})
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
