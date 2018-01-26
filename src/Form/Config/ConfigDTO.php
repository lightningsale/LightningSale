<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 04.01.18
 * Time: 17:48
 */

namespace App\Form\Config;


class ConfigDTO
{
    public $locale;
    public $currency;
    public $invoice_timeout;

    public function __construct(string $locale, string $currency, string $invoice_timeout)
    {
        $this->locale = $locale;
        $this->currency = $currency;
        $this->invoice_timeout = $invoice_timeout;
    }


}