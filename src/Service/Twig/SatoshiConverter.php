<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 02.01.18
 * Time: 08:46
 */

namespace App\Service\Twig;


use App\Exchange\CoinMarketCap;
use App\Exchange\Exchange;
use App\Repository\ConfigRepository;
use App\Service\ExchangeService;
use Symfony\Component\Intl\Intl;

class SatoshiConverter extends \Twig_Extension
{
    private const SATOSHI_IN_BTC = 100000000.0;
    public const LOCALE_NOK = "nb_NO.utf8";
    public const LOCALE_USD = "en_US.utf8";
    private $configRepository;
    private $exchangeService;

    /**
     * ConvertSatoshi constructor.
     */
    public function __construct(ExchangeService $exchangeService, ConfigRepository $configRepository)
    {
        $this->configRepository = $configRepository;
        $this->exchangeService = $exchangeService;

    }


    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter("satoshiToMilliBtc", [$this, 'satoshiToMilliBtc'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter("satoshiToLocal", [$this, 'satoshiToLocal'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter("formatSatoshi", [$this, 'formatSatoshi']),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction("currencySymbol", [$this, 'currencySymbol']),
        ];
    }


    public function satoshiToMilliBtc(string $satoshi, int $round = 2)
    {
        $satoshi = (float) $satoshi;
        $mBTC = $satoshi / self::SATOSHI_IN_BTC * 1000;
        return round($mBTC, $round);
    }

    public function satoshiToLocal($satoshi, int $round = 2) {
        $price = $this->getExhange()->getBuyPrice($this->getCurrency());
        $price = (float) $satoshi / self::SATOSHI_IN_BTC * (float) $price;
        return round($price, $round);
    }

    public function localToSatoshi(float $local): float
    {
        $price = (float) $this->getExhange()->getBuyPrice();
        return round($local / $price * self::SATOSHI_IN_BTC, 0);
    }

    public function formatSatoshi($satoshi)
    {
        $value = $this->satoshiToLocal($satoshi, 4);
        $formatter = \NumberFormatter::create($this->getLocale(), \NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($value, $this->getCurrency());
    }

    public function currencySymbol() {
        return Intl::getCurrencyBundle()->getCurrencySymbol($this->getCurrency());
    }

    private function getLocale()
    {
        return $this->configRepository->getConfig(ConfigRepository::LOCALE, self::LOCALE_USD)->getValue();
    }

    private function getCurrency()
    {
        return $this->configRepository->getConfig(ConfigRepository::CURRENCY, "USD")->getValue();
    }

    private function getExhange(): Exchange
    {
        if (isset($this->exchange))
            return $this->exchange;

        $exchange = $this->configRepository->getConfig(ConfigRepository::EXCHANGE, CoinMarketCap::class)->getValue();
        $this->exchange = $this->exchangeService->getExchange($exchange);
        return $this->exchange;
    }
}