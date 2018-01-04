<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 28.12.17
 * Time: 22:27
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use LightningSale\LndRest\Model\AddInvoiceResponse;
use LightningSale\LndRest\Model\Invoice as LndInvoice;
use LightningSale\LndRest\Resource\LndClient;

/**
 * Class Transaction
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="invoices")
 */
class Invoice
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $rHashString;

    /**
     * @var Cashier|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Cashier", cascade={"persist"}, inversedBy="invoices")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $createdBy;
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getRHashString(): string
    {
        return $this->rHashString;
    }

    public function getCreatedBy(): ?Cashier
    {
        return $this->createdBy;
    }
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function __construct(string $rHashString, Cashier $cashier)
    {
        $this->rHashString = $rHashString;
        $this->createdBy = $cashier;
        $this->createdAt = new \DateTime();
    }

    public static function fromAddInvoiceResponse(AddInvoiceResponse $addInvoiceResponse, Cashier $user): self
    {
        return new Invoice($addInvoiceResponse->getRHash(), $user);
    }

    public function getInvoice(LndClient $lndClient): LndInvoice
    {
        return $lndClient->lookupInvoice($this->rHashString);
    }
}