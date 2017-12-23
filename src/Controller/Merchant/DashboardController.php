<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 12:52
 */

namespace App\Controller\Merchant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 * @package App\Controller\Merchant
 * @Route("/dashbard", name="merchant_dashboard_")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(): Response
    {
        return $this->render(":Merchant/Dashboard:index.html.twig", []);
    }
}