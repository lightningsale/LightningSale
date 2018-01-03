<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 02.01.18
 * Time: 08:46
 */

namespace App\Service\Twig;


use App\Exchange\CoinMarketCap;

class ConvertSatoshi extends \Twig_Extension
{
    private const SATOSHI_IN_BTC = 100000000;
    private $coinMarketCap;

    /**
     * ConvertSatoshi constructor.
     */
    public function __construct(CoinMarketCap $coinMarketCap)
    {
        $this->coinMarketCap = $coinMarketCap;
    }


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
        return round((float) $satoshi / self::SATOSHI_IN_BTC / 1000, $round);
    }

    public function satoshiToLocal(string $satoshi, int $round = 2, string $currency = "NOK") {
        $price = $this->coinMarketCap->getBuyPrice($currency);
        $price = (float) $satoshi / self::SATOSHI_IN_BTC * (float) $price;
        return round($price, $round);
    }

    public function localToSatoshi(string $local, string $currency = "NOK")
    {
        $local = (float) $local;
        $price = (float) $this->coinMarketCap->getBuyPrice($currency);
        return round($local / $price * self::SATOSHI_IN_BTC, 0);
    }

    public function localCurrency()
    {
        return "kr";
    }
}