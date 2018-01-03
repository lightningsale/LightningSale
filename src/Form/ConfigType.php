<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 03.01.18
 * Time: 17:00
 */

namespace App\Form;


use App\Service\ExchangeService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigType extends AbstractType
{

    private $exchangeService;

    /**
     * ConfigType constructor.
     * @param $exchangeService
     */
    public function __construct(ExchangeService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $symbols = [];
        foreach ($this->exchangeService->getExchanges() as $exchange) {
            $temp = [];
            foreach ($exchange->getSymbols() as $symbol)
                $temp[$symbol] = get_class($exchange) . "::$symbol";

            $symbols[$exchange->getName()] = $temp;
        }

        $builder
            ->add("currency", ChoiceType::class, ['choices' => $symbols])
            ->add("invoice_timeout", TextType::class, ['data' => '3600', 'label' => 'Invoice timeout (seconds)'])
            ->add("save", SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

}