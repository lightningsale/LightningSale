<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 14.01.18
 * Time: 14:03
 */

namespace App\Form\Profile;


use App\Entity\Cashier;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProfileType extends AbstractType
{
    private $token;
    private $userRepo;

    public function __construct(TokenStorageInterface $tokenStorage, UserRepository $userRepo)
    {
        $this->token = $tokenStorage->getToken();
        $this->userRepo = $userRepo;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->token ? $this->token->getUser() : null;

        if (!$user instanceof Cashier)
            throw new AccessDeniedException();

        $uniqueEmailConstraint = function ($email, ExecutionContextInterface $context) use ($user)
        {
            $existingUser = $this->userRepo->findByEmail($email);
            if ($existingUser && $existingUser !== $user) {
                $context->addViolation("Email is already in use");
            }
        };


        $builder
            ->add("email", EmailType::class, [
                'data' => $user->getEmail(),
                'constraints' => [
                    new Callback($uniqueEmailConstraint),
                    new Email()
                ]
            ])
            ->add("save", SubmitType::class)
        ;
    }

}