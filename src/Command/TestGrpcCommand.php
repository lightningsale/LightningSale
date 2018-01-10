<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 21.12.17
 * Time: 09:44
 */

namespace App\Command;


use App\Exchange\CoinMarketCap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestGrpcCommand extends Command
{
    private $cap;

    public function __construct(CoinMarketCap $cap)
    {
        parent::__construct("app:test");
        $this->cap = $cap;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->cap->getBuyPrice("USD"));
    }

}