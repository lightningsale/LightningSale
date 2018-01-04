<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 02.01.18
 * Time: 08:46
 */

namespace App\Service\Twig;


use App\Exchange\CoinMarketCap;
use App\Repository\ConfigRepository;

class SatoshiConverter extends \Twig_Extension
{
    private const SATOSHI_IN_BTC = 100000000;
    private $coinMarketCap;
    public const LOCALE_NOK = "nb_NO.utf8";
    public const LOCALE_USD = "en_US.utf8";
    private $currency;
    private $locale;

    /**
     * ConvertSatoshi constructor.
     */
    public function __construct(CoinMarketCap $coinMarketCap, ConfigRepository $configRepository)
    {
        $this->coinMarketCap = $coinMarketCap;
        $this->locale = $configRepository->getConfig(ConfigRepository::LOCALE, self::LOCALE_USD)->getValue();
        $this->currency = $configRepository->getConfig(ConfigRepository::CURRENCY, "USD")->getValue();
    }


    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter("satoshiToMilliBtc", [$this, 'satoshiToMilliBtc'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter("satoshiToLocal", [$this, 'satoshiToLocal'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter("formatSatoshi", [$this, 'formatSatoshi'])
        ];
    }

    public function satoshiToMilliBtc(string $satoshi, int $round = 2)
    {
        return round((float) $satoshi / self::SATOSHI_IN_BTC / 1000, $round);
    }

    public function satoshiToLocal($satoshi, int $round = 2) {
        $price = $this->coinMarketCap->getBuyPrice($this->currency);
        $price = (float) $satoshi / self::SATOSHI_IN_BTC * (float) $price;
        return round($price, $round);
    }

    public function localToSatoshi(string $local)
    {
        $local = (float) $local;
        $price = (float) $this->coinMarketCap->getBuyPrice();
        return round($local / $price * self::SATOSHI_IN_BTC, 0);
    }

    public function formatSatoshi($satoshi)
    {
        $value = $this->satoshiToLocal($satoshi, 4);
        $formatter = \NumberFormatter::create($this->locale, \NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($value, $this->currency);
    }
}