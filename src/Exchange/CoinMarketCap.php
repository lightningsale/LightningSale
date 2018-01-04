<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 03.01.18
 * Time: 20:35
 */

namespace App\Exchange;


use GuzzleHttp\Client;
use Symfony\Component\Cache\Simple\AbstractCache;

class CoinMarketCap implements Exchange
{
    private $cache;
    private $symbols = [];

    public function __construct(AbstractCache $cache)
    {
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
        if (!$this->cache->has("coinmarketcap.$symbol")) {
            $price = $this->updatePrice($symbol);
            $this->cache->set("coinmarketcap.$symbol", $price,300);
            return $price;
        }

        return (string) $this->cache->get("coinmarketcap.$symbol");
    }

    public function getSellPrice(string $symbol = "USD"): string
    {
        return $this->getBuyPrice($symbol);
    }

    private function updatePrice(string $symbol): string
    {
        if (isset($this->symbols[$symbol]))
            return $this->symbols[$symbol];

        $client = new Client();
        $response = $client->get("https://api.coinmarketcap.com/v1/ticker/bitcoin/?convert=$symbol");
        $body = $response->getBody()->getContents();
        $prices = \GuzzleHttp\json_decode($body, true);
        $price = array_shift($prices);
        $key = "price_" . mb_strtolower($symbol);
        $this->symbols[$symbol] = $price[$key];
        return $price[$key];
    }
}