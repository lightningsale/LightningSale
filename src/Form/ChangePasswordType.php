<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 14.01.18
 * Time: 14:03
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("old_password", PasswordType::class)
            ->add("new_password", PasswordType::class)
            ->add("repeat_password", PasswordType::class)
            ->add("save", SubmitType::class)
        ;
    }

}