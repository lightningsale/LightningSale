<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 03.01.18
 * Time: 17:59
 */

namespace App\Exchange;


use GuzzleHttp\Client;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\CacheItem;

class BitmyntNo implements Exchange
{
    /** @var CacheItem */
    private $item;
    private $cache;

    public function __construct(AbstractAdapter $cache)
    {
        $this->item = $cache->getItem("bitmynt.no");
        $this->item->expiresAfter(120);
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

        if (!$this->item->isHit())
            $this->updatePrices();

        $prices = $this->item->get();
        return $prices[$symbol]["buy"];
    }

    public function getSellPrice(string $symbol = "BTCNOK"): string {
        $this->assertSymbol($symbol);

        if (!$this->item->isHit())
            $this->updatePrices();

        $prices = $this->item->get();
        return $prices[$symbol]["sell"];
    }

    private function updatePrices(): void
    {
        $client = new Client();
        $response = $client->get("http://bitmynt.no/ticker.pl");
        $body = $response->getBody()->getContents();

        $prices = \GuzzleHttp\json_decode($body, true);
        $this->item->set([
            "BTCNOK" => [
                'buy' => $prices['nok']['buy'],
                'sell' => $prices['nok']['sell'],
            ],
            'BTCEUR' => [
                'buy' => $prices['eur']['buy'],
                'sell' => $prices['eur']['sell'],
            ]
        ]);

        $this->cache->save($this->item);
    }

    private function assertSymbol(string $symbol): void
    {
        if (!in_array($symbol, $this->getSymbols(), true))
            throw new \InvalidArgumentException("Symbol($symbol) not supported by " . self::class);
    }
}