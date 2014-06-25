<?php

namespace Store\OrderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * @Route("/orders")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/viewbase", name="orders_view_base")
     * @Template()
     */
    public function indexAction()
    {
        $rootDir = $this->get('kernel')->getRootDir();
        $invoicesDir = $rootDir.'/../web/uploads/invoices';

        $fs = new Filesystem();

        if (!$fs->exists($invoicesDir)) {
            try {
                $fs->mkdir($invoicesDir);
            } catch (IOException $error) {
                echo "An error occured while creating your directory";
            }
        }

        return array(
            'date' => new \DateTime("NOW"),
        );
    }

    /**
     * @Route("/download", name="invoice_download")
     */
    public function downloadInvoiceAction()
    {
        $rootDir = $this->get('kernel')->getRootDir();
        $invoicesDir = $rootDir.'/../web/uploads/invoices';

        $fs = new Filesystem();

        if (!$fs->exists($invoicesDir)) {
            try {
                $fs->mkdir($invoicesDir);
            } catch (IOException $error) {
                echo "An error occured while creating your directory";
            }
        }

        $html = $this->renderView('StoreOrderBundle:Default:index.html.twig', array(
            'date'  => new \DateTime("NOW")
        ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'render; filename="invoice12344.pdf"'
            )
        );
    }
}
