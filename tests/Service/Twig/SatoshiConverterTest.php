<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 13.01.18
 * Time: 17:44
 */

namespace App\Tests\Service\Twig;

use App\Entity\Config;
use App\Exchange\Exchange;
use App\Repository\ConfigRepository;
use App\Service\ExchangeService;
use App\Service\Twig\SatoshiConverter;
use PHPUnit\Framework\TestCase;

class SatoshiConverterTest extends TestCase
{
    private $converter;

    private const SATOSHI_IN_BITCOIN = 100000000;
    private const BTC_10_IN_SATOSHI  = 1000000000;

    public function setUp()
    {
        $exchangeService = $this->createMock(ExchangeService::class);
        $configRepository = $this->createMock(ConfigRepository::class);
        $configRepository->method("getConfig")->willReturnCallback(function($key, $default) {
            switch ($key) {
                case ConfigRepository::CURRENCY: return new Config('','NOK');
                case ConfigRepository::EXCHANGE: return new Config('','CoinMarketCap');
                case ConfigRepository::LOCALE:   return new Config('',SatoshiConverter::LOCALE_NOK);
                default: return new Config("", "wtf");
            }
        });
        $exchange = $this->createMock(Exchange::class);
        $exchangeService->method("getExchange")->willReturn($exchange);
        $exchange->method("getBuyPrice")->willReturn("200");
        $exchange->method("getSellPrice")->willReturn("400");
        $this->converter = new SatoshiConverter($exchangeService, $configRepository);
    }

    public function testSatoshiToMilliBtc()
    {
        $mBtc = $this->converter->satoshiToMilliBtc(self::BTC_10_IN_SATOSHI);
        self::assertEquals(10000, $mBtc);
    }

    public function testSatoshiToLocal()
    {
        $local = $this->converter->satoshiToLocal(self::BTC_10_IN_SATOSHI);
        self::assertEquals(2000, $local);
    }

    public function testLocalToSatoshi()
    {
        $satoshi = $this->converter->localToSatoshi(600);
        self::assertEquals(3 * self::SATOSHI_IN_BITCOIN, $satoshi);
    }

    public function testFormatSatoshi()
    {
        $format = $this->converter->formatSatoshi(self::SATOSHI_IN_BITCOIN);
        self::assertEquals("kr 200,00", $format);
    }

    public function testCurrencySymbol()
    {
        $symbol = $this->converter->currencySymbol();
        self::assertEquals("kr", $symbol);
    }
}
