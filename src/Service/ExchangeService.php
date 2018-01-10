<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 03.01.18
 * Time: 18:25
 */

namespace App\Service;


use App\Exchange\Exchange;

class ExchangeService
{
    private $exchanges;

    /**
     * ExchangeService constructor.
     * @param Exchange[] $exchanges
     */
    public function __construct(\IteratorAggregate $exchanges)
    {
        $this->exchanges = $exchanges;
    }

    /**
     * @return Exchange[]|array
     */
    public function getExchanges()
    {
        return $this->exchanges;
    }

    public function getExchange($exchange): Exchange
    {
        foreach ($this->exchanges as $e)
            if (get_class($e) === $exchange)
                return $e;

        throw new \DomainException("Could not find exchange $exchange!");
    }
}