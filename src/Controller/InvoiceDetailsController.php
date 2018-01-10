<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 29.12.17
 * Time: 16:28
 */

namespace App\Controller;


use LightningSale\LndClient\Client as LndRestClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InvoiceDetailsController
 * @package App\Controller
 * @Route("/dashboard/invoices/{rHash}", name="invoice_details_", requirements={"rHash"=".*"})
 * @Security("is_granted('ROLE_CASHIER')")
 */
class InvoiceDetailsController extends Controller
{
    /**
     * @Route("", name="index")
     */
    public function indexAction(string $rHash, LndRestClient $lndClient): Response
    {
        return $this->render("InvoiceDetails/index.html.twig",[
            'invoice' => $lndClient->lookupInvoice($rHash)
        ]);
    }
}