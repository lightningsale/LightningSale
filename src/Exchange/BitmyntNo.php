<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 03.01.18
 * Time: 17:59
 */

namespace App\Exchange;


use GuzzleHttp\Client;
use Symfony\Component\Cache\Simple\AbstractCache;

class BitmyntNo implements Exchange
{
    private $cache;
    private $symbols = [];

    public function __construct(AbstractCache $cache)
    {
        $this->cache = $cache;
    }

    public function getName(): string
    {
        return "BitMynt";
    }

    public function getSymbols(): array
    {
        return ['NOK', 'EUR'];
    }

    public function getBuyPrice(string $symbol = "BTCNOK"): string {
        $this->assertSymbol($symbol);

        if (!$this->cache->has("bitmynt.$symbol")){
            $prices = $this->getPrice($symbol);
            $this->cache->set("bitmynt.$symbol", $prices, 200);
            return $prices['buy'];
        }

        return $this->cache->get("bitmynt.$symbol")["buy"];
    }

    public function getSellPrice(string $symbol = "BTCNOK"): string {
        $this->assertSymbol($symbol);

        if (!$this->cache->has("bitmynt.$symbol")){
            $prices = $this->getPrice($symbol);
            $this->cache->set("bitmynt.$symbol", $prices, 200);
            return $prices['sell'];
        }

        return $this->cache->get("bitmynt.$symbol")["sell"];
    }

    private function getPrice($symbol): array
    {
        if (isset($this->symbols[$symbol]))
            return $this->symbols[$symbol];

        $client = new Client();
        $response = $client->get("http://bitmynt.no/ticker.pl");
        $body = $response->getBody()->getContents();

        $prices = \GuzzleHttp\json_decode($body, true);
        switch ($symbol) {
            case "NOK": return $this->symbols[$symbol] = $prices['nok'];
            case "EUR": return $this->symbols[$symbol] = $prices['eur'];
            default: throw new \InvalidArgumentException("$symbol not supported by Bitmynt.no!");
        }
    }

    private function assertSymbol(string $symbol): void
    {
        if (!in_array($symbol, $this->getSymbols(), true))
            throw new \InvalidArgumentException("Symbol($symbol) not supported by " . self::class);
    }
}