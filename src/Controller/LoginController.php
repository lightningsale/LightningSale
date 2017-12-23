<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 13:08
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerTrait;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LoginController
 * @package App\Controller
 * @Route("/", name="login_")
 */
class LoginController extends Controller
{

    /**
     * @Route("/", name="merchant")
     */
    public function merchantLoginAction() {
        return $this->render("Login/merchant.html.twig");
    }
}