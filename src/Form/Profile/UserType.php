<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 25.01.18
 * Time: 20:19
 */

namespace App\Form\Profile;


use App\Entity\Cashier;
use App\Entity\Merchant;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserType extends AbstractType
{
    private $tokenStorage;
    private $userRepo;

    /**
     * UserType constructor.
     * @param $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage, UserRepository $userRepo)
    {
        $this->tokenStorage = $tokenStorage->getToken();
        $this->userRepo = $userRepo;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Cashier $user */
        $user = $options['user'];

        $loggedInUser = $this->tokenStorage->getUser();

        $uniqueEmailConstraint = function ($email, ExecutionContextInterface $context) use ($user)
        {
            $existingUser = $this->userRepo->findByEmail($email);
            if ($existingUser && $existingUser !== $user) {
                $context->addViolation("Email is already in use");
            }
        };

        $builder
            ->add("email", EmailType::class, [
                'attr' => ['readonly' => $user === $loggedInUser],
                'data' => $user->getEmail(),
                'constraints' => [new Callback($uniqueEmailConstraint)],
            ])
            ->add("role", ChoiceType::class, [
                'attr' => ['readonly' => $user === $loggedInUser],
                'data' => $user instanceof Merchant ? "merchant" : "cashier",
                'choices' => [
                    'Administrator' => Merchant::class,
                    'User' => Cashier::class
                ],
                'label_attr' => ['class' => 'col-form-label'], //HOTFIX since symfony bootstrap adds the wrong class
                'multiple' => false,
                'expanded' => true,
            ])
            ->add("save", SubmitType::class, ['disabled' => $user === $loggedInUser])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => UserDTO::class,
                'user' => null,
            ])
            ->setRequired('user')
        ;
    }


}