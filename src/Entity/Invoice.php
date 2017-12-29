<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 28.12.17
 * Time: 22:27
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
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
     * @ORM\Column(type="json")
     */
    private $invoice;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $rHashString;

    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * Transaction constructor.
     */
    public function __construct(LndInvoice $invoice)
    {
        $this->rHashString = $invoice->getRHash();
    }

    public function getInvoice(LndClient $lndClient): LndInvoice
    {
        return $lndClient->lookupInvoice($this->rHashString);
    }
}