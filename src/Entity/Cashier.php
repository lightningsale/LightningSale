<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 17:44
 */

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use LightningSale\LndRest\LndRestClient;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * Class Cashier
 * @package App\Entity
 * @ORM\Entity()
 */
class Cashier extends User
{
    /**
     * @var Invoice[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\Invoice", cascade={"persist"}, mappedBy="createdBy")
     */
    protected $invoices;

    /**
     * @return Invoice[]|ArrayCollection
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    public function __construct(string $email, EncoderFactoryInterface $encoderFactory, string $rawPassword)
    {
        parent::__construct($email, $encoderFactory, $rawPassword);

        $this->invoices = new ArrayCollection();
    }

    public function getRoles(): array
    {
        return ["ROLE_CASHIER"];
    }

    public function createInvoice(LndRestClient $lndClient, string $amount, string $memo = "", int $timeout = 3600): Invoice
    {
        $addInvoiceResponse = $lndClient->addInvoice($memo, $amount, $timeout);

        $invoice = Invoice::fromAddInvoiceResponse($addInvoiceResponse, $this);
        $this->invoices->add($invoice);

        return $invoice;
    }
}