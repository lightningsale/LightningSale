<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 13:08
 */

namespace App\Controller;


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
     */
    public function merchantLoginAction(AuthenticationUtils $authUtils) {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        $class = $error ? 'is-invalid' : '';

        $form = $this->createFormBuilder()
            ->add("_username", TextType::class)
            ->add("_password", PasswordType::class, ['attr' => ['class' => $class]])
            ->add("save", SubmitType::class, ['label' => 'Sign in'])
            ->getForm();

        if ($error) {
            $passwordField = $form->get("_password");
            $passwordField->addError(new FormError($error->getMessage()));
        }

        return $this->render("Login/merchant.html.twig", [
            'last_username' => $lastUsername,
            'error'         => $error,
            'form'          => $form->createView(),
        ]);
    }


}