<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 28.12.17
 * Time: 22:27
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use LightningSale\LndClient\Client as LndRestClient;
use LightningSale\LndClient\Model\AddInvoiceResponse;

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
     * @var resource
     * @ORM\Column(type="blob", length=32)
     */
    private $rHash;

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

    public function getRHash(): string
    {
        return stream_get_contents($this->rHash, 32);
    }

    public function getRHashStr(): string
    {
        return bin2hex($this->getRHash());
    }

    public function getCreatedBy(): ?Cashier
    {
        return $this->createdBy;
    }
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function __construct(string $rHash, Cashier $cashier)
    {
        $this->rHash = $rHash;
        $this->createdBy = $cashier;
        $this->createdAt = new \DateTime();
    }

    public static function fromAddInvoiceResponse(AddInvoiceResponse $addInvoiceResponse, Cashier $user): self
    {
        return new Invoice($addInvoiceResponse->getRHash(), $user);
    }

    public function getInvoice(LndRestClient $lndClient): \LightningSale\LndClient\Model\Invoice
    {
        return $lndClient->lookupInvoice($this->rHash);
    }
}