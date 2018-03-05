<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 02.01.18
 * Time: 22:46
 */

namespace App\Form\Config;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class WithdrawFundsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$addr, $amount, $targetConf, $satPerByte
        $builder
            ->add("address", TextType::class, ['mapped' => false])
            ->add("amount", NumberType::class, ['mapped' => false, 'attr' => ['placeholder' => 'satoshi']])
            ->add("fee", NumberType::class, ['mapped' => false, 'attr' => ['placeholder' => 'satoshi pr byte']])
            ->add("save", SubmitType::class, ['label' => 'Withdraw'])
            ;
    }
}