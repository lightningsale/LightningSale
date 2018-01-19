<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 25.01.18
 * Time: 19:37
 */

namespace App\Repository;


use App\Entity\Cashier;
use LightningSale\LndClient\Client;
use LightningSale\LndClient\Model\Invoice;

class LndInvoiceRepository
{
    private $lndClient;

    public function __construct(Client $lndClient)
    {
        $this->lndClient = $lndClient;
    }


    /**
     * @return Invoice[]
     */
    public function findAll(bool $pendingOnly = false): array
    {
        return $this->lndClient->listInvoices($pendingOnly);
    }

    public function findByUser(Cashier $cashier, bool $pendingOnly = false): array
    {
        $rHashes = $cashier->getInvoices()->map(function(\App\Entity\Invoice $i) {
            return $i->getRHash();
        });

        return array_filter($this->findAll($pendingOnly), function(Invoice $i) use ($rHashes) {
             return in_array($i->getRHash(), $rHashes, true);
        });
    }
}