<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 14.01.18
 * Time: 14:03
 */

namespace App\Form;


use App\Entity\Cashier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileType extends AbstractType
{
    private $router;
    private $token;

    public function __construct(RouterInterface $router, TokenStorageInterface $tokenStorage)
    {
        $this->router = $router;
        $this->token = $tokenStorage->getToken();
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->token ? $this->token->getUser() : null;

        if (!$user instanceof Cashier)
            throw new AccessDeniedException();

        $builder
            ->add("email", EmailType::class, ['data' => $user->getEmail()])
            ->add("save", SubmitType::class)
            ->setAction($this->router->generate("profile_change_email"))
        ;
    }

}