<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 21.12.17
 * Time: 09:44
 */

namespace App\Command\Lightning;


use LightningSale\LndClient\Client as LndRestClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WalletBalance extends Command
{
    private $lndClient;

    public function __construct(LndRestClient $lndClient)
    {
        $this->lndClient = $lndClient;
        parent::__construct("lightning:wallet");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        dump($this->lndClient->walletBalance());

    }

}