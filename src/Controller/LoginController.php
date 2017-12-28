<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 13:08
 */

namespace App\Controller;


use App\Form\Security\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
    public function merchantLoginAction() {

        $form = $this->createForm(LoginType::class);
        return $this->render("Login/merchant.html.twig", [
            'form'          => $form->createView(),
        ]);
    }


}