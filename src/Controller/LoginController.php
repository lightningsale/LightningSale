<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 13:08
 */

namespace App\Controller;


use App\Form\Security\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class LoginController
 * @package App\Controller
 * @Route("/", name="login_")
 */
class LoginController extends Controller
{

    /**
     * @Route("/", name="merchant")
     * @Route("/login_check", name="check")
     */
    public function merchantLoginAction(UserInterface $user = null) {
        if ($user)
            return $this->redirectToRoute("cashier_dashboard_index");


        $form = $this->createForm(LoginType::class);
        return $this->render("Login/merchant.html.twig", [
            'form'          => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="out")
     */
    public function logoutAction(): void {}


}