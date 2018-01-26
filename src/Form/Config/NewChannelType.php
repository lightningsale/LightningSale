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

class NewChannelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("amount", NumberType::class)
            ->add("pubkey", TextType::class)
            ->add("host", TextType::class)
            ->add("save", SubmitType::class, ['label' => 'Connect'])
            ;
    }

}