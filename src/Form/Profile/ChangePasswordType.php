<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 14.01.18
 * Time: 14:03
 */

namespace App\Form\Profile;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("oldPassword", PasswordType::class)
            ->add("newPassword", PasswordType::class)
            ->add("repeatPassword", PasswordType::class)
            ->add("save", SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChangePasswordDTO::class
        ]);
    }


}