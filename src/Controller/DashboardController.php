<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 12:52
 */

namespace App\Controller;
use App\Entity\Payment;
use LightningSale\LndRest\Resource\LndClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 * @package App\Controller\Cashier
 * @Route("/dashboard", name="cashier_dashboard_")
 * @Security("is_granted('ROLE_CASHIER')")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(LndClient $lndClient): Response
    {
        $invoices = $lndClient->listInvoices();

        return $this->render("Dashboard/index.html.twig", [
            'invoices' => $invoices
        ]);
    }
}