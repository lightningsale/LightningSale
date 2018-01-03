<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 03.01.18
 * Time: 20:35
 */

namespace App\Exchange;


use GuzzleHttp\Client;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\CacheItem;

class CoinMarketCap implements Exchange
{

    /** @var CacheItem */
    private $item;
    private $cache;

    public function __construct(AbstractAdapter $cache)
    {
        $this->item = $cache->getItem("coinmarketcap");
        $this->item->expiresAfter(300);
        $this->cache = $cache;
    }

    public function getName(): string
    {
        return "CoinMarketCap";
    }

    /**
     * @return string[]
     */
    public function getSymbols(): array
    {
        return ["AUD", "BRL", "CAD", "CHF", "CLP", "CNY", "CZK", "DKK", "EUR", "GBP", "HKD", "HUF", "IDR", "ILS", "INR", "JPY", "KRW", "MXN", "MYR", "NOK", "NZD", "PHP", "PKR", "PLN", "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "ZAR"];
    }

    public function getBuyPrice(string $symbol = "USD"): string
    {
        $price = $this->item->get();
        $key = "price_" . mb_strtolower($symbol);
        if (!$this->item->isHit() || !isset($price[$key]))
            $this->updatePrice($symbol);


        $price = $this->item->get();
        return $price[$key];
    }

    public function getSellPrice(string $symbol = "USD"): string
    {
        $price = $this->cache->getItem("coinmarketcap")->get();
        $key = "price_" . mb_strtolower($symbol);
        if (!$this->item->isHit() || !isset($price[$key]))
            $this->updatePrice($symbol);


        $price = $this->item->get();
        return $price[$key];
    }

    private function updatePrice(string $symbol): void
    {
        $client = new Client();
        $response = $client->get("https://api.coinmarketcap.com/v1/ticker/bitcoin/?convert=$symbol");
        $body = $response->getBody()->getContents();
        $prices = \GuzzleHttp\json_decode($body, true);
        $price = array_shift($prices);
        $this->item->set($price);
        $this->cache->save($this->item);
    }
}