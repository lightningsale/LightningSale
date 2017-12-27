<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 12:52
 */

namespace App\Controller\Merchant;
use App\Entity\Payment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 * @package App\Controller\Merchant
 * @Route("/dashboard", name="merchant_dashboard_")
 * @Security("is_granted('ROLE_CASHIER')")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(): Response
    {
        /** @var Payment[] $invoices */
        $invoices = [];
        foreach (range(0,100) as $index)
            $invoices[] = new Payment(Payment::STATE_PAID, random_int(100,1000)* 10,"NOK");

        return $this->render("Merchant/Dashboard/index.html.twig", ['invoices' => $invoices]);
    }
}