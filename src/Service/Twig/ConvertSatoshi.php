<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 02.01.18
 * Time: 08:46
 */

namespace App\Service\Twig;


class ConvertSatoshi extends \Twig_Extension
{
    private const SATOSHI_IN_BTC = 100000;

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter("satoshiToMilliBtc", [$this, 'satoshiToMilliBtc'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter("satoshiToLocal", [$this, 'satoshiToLocal'], ['is_safe' => ['html']])
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction("localCurrency", [$this, 'localCurrency'])
        ];
    }

    public function satoshiToMilliBtc(string $satoshi, int $round = 2)
    {
        return round((float) $satoshi / self::SATOSHI_IN_BTC, $round);
    }

    public function satoshiToLocal(string $satoshi, int $round = 2, string $currency = null) {
        return $this->satoshiToMilliBtc($satoshi, $round);
    }

    public function localCurrency()
    {
        return "mBTC"; //TODO: Update this!
    }
}