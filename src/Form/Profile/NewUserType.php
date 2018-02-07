<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 25.01.18
 * Time: 20:19
 */

namespace App\Form\Profile;


use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class NewUserType extends AbstractType
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $uniqueEmailConstraint = function ($email, ExecutionContextInterface $context)
        {
            $existingUser = $this->userRepo->findByEmail($email);
            if ($existingUser) {
                $context->addViolation("Email is already in use");
            }
        };

        $builder
            ->add("email", EmailType::class, [
                'constraints' => [new Callback($uniqueEmailConstraint)],
            ])
            ->add("newPassword", PasswordType::class)
            ->add("repeatPassword", PasswordType::class)
            ->add("role", ChoiceType::class, [
                'choices' => [
                    'Administrator' => true,
                    'User' => false
                ],
                'label_attr' => ['class' => 'col-form-label'], //HOTFIX since symfony bootstrap adds the wrong class
                'multiple' => false,
                'expanded' => true,
            ])
            ->add("save", SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => NewUserDTO::class,
                'user' => null,
            ])
            ->setRequired('user')
        ;
    }


}