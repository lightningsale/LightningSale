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
            new \Twig_SimpleFilter("satoshiToMilliBtc", [$this, 'satoshiToMilliBtc']),
            new \Twig_SimpleFilter("satoshiToLocal", [$this, 'satoshiToLocal'])
        ];
    }

    public static function satoshiToMilliBtc(string $satoshi, int $round = 2)
    {
        return round((float) $satoshi / self::SATOSHI_IN_BTC, $round);
    }

    public static function satoshiToLocal(string $satoshi, int $round = 2, string $currency = null) {
        return self::satoshiToMilliBtc($satoshi, $round);
    }
}