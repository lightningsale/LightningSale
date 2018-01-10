<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 29.12.17
 * Time: 16:20
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class NewInvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("description", TextType::class, ['required' => false])
            ->add("amount", NumberType::class)
            ;
    }

}