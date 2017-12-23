<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 14:01
 */

namespace App\Entity;


class Payment
{
    public const STATE_PAID = 1;
    public const STATE_WAITING = 0;
    public const STATE_PROCESSING = 2;

    /**
     * @var \DateTime
     */
    private $createdAt;
    /**
     * @var int
     */
    private $state;
    /**
     * @var \DateTime|null
     */
    private $payedAt;
    /**
     * @var int
     */
    private $amount;
    /**
     * @var string
     */
    private $currency;


    public function getQrCodeMessage(): string
    {
        return "lightning:lntb91u1pdrukf6pp5l5hsg52tnlxc60w8pha2szp3ypuadls2hpw8ysmclr6a3gctnw6sdzvxgsy2umswfjhxum0yppk76twypgxzmnwvykzqveq2d3kzmrpyppks6tsypr8yctswp6kxcmfdehsnhrr7s5724rr0rsv2vgfeprrtp69g7algnd27972xts9ee4duz7xaq42t568mgnmadwgqtmy6n98rr9vm6trxaw4r7yf0um98nqepjgp47675k";
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function getNiceState(): string
    {
        switch ($this->state) {
            case self::STATE_PAID: return "Paid";
            case self::STATE_PROCESSING: return "Processing";
            case self::STATE_WAITING: return "Waiting";
            default: throw new \DomainException("Unkown state!");
        }
    }

    public function getPayedAt(): ?\DateTime
    {
        return $this->payedAt;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getNiceAmount(): string
    {
        return sprintf("%.2f", $this->amount / 100);
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function __construct(int $state, int $amount, string $currency)
    {
        $this->createdAt = new \DateTime();
        $this->state = $state;
        $this->amount = $amount;
        $this->currency = $currency;
    }
}