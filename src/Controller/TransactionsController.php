<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 29.12.17
 * Time: 17:01
 */

namespace App\Controller;


use LightningSale\LndRest\LndClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TransactionsController
 * @package App\Controller
 * @Route("/dashboard/settings/transactions", name="transactions_")
 * @Security("is_granted('ROLE_MERCHANT')")
 */
class TransactionsController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(LndClient $lndClient): Response
    {
        return $this->render("Transactions/index.html.twig", [
            'transactions' => $lndClient->getTransactions(),
        ]);
    }
}