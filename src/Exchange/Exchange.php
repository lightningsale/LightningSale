<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 03.01.18
 * Time: 18:19
 */

namespace App\Exchange;

interface Exchange
{

    public function getName(): string;

    /**
     * @return string[]
     */
    public function getSymbols(): array;

    public function getBuyPrice(string $symbol = "NOK"): string;

    public function getSellPrice(string $symbol = "NOK"): string;
}