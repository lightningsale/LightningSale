<?php
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
}