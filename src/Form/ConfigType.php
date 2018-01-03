<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 03.01.18
 * Time: 17:00
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("currency", ChoiceType::class, ['choices' => [
                'NOK' => 'NOK',
                'USD' => 'USD',
                'EUR' => 'EUR',
                'BTC' => 'BTC',
                'mBTC' => 'mBTC'
            ]])
            ->add("save", SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

}