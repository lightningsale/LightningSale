<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 27.12.17
 * Time: 21:24
 */

namespace App\Form\Security;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginType extends AbstractType
{
    private $authUtils;
    private $router;

    /**
     * LoginType constructor.
     * @param $authUtils
     */
    public function __construct(AuthenticationUtils $authUtils, RouterInterface $router)
    {
        $this->authUtils = $authUtils;
        $this->router = $router;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $error = $this->authUtils->getLastAuthenticationError(false);
        $lastUsername = $this->authUtils->getLastUsername();
        $class = $error ? 'is-invalid' : '';


        $builder
            ->add("username", TextType::class, ['data' => $lastUsername])
            ->add("password", PasswordType::class, ['attr' => ['class' => $class]])
            ->add("save", SubmitType::class, ['label' => 'Sign in'])
            ->setAction($this->router->generate("login_check"))
            ;

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $error = $this->authUtils->getLastAuthenticationError();
        if ($error) {
            $passwordField = $form->get("password");
            $passwordField->addError(new FormError($error));
        }
    }
}