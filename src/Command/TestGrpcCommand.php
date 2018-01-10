<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 21.12.17
 * Time: 09:44
 */

namespace App\Command;


use App\Exchange\CoinMarketCap;
use App\Service\ExchangeService;
use GuzzleHttp\Client;
use LightningSale\LndRest\Model\SendRequest;
use LightningSale\LndRest\LndClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestGrpcCommand extends Command
{
    private $exchangeService;

    public function __construct(ExchangeService $exchangeService, CoinMarketCap $cap)
    {
        parent::__construct("app:test");
        $this->exchangeService = $exchangeService;
        $this->cap = $cap;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->cap->getBuyPrice("USD"));
        die;
        //{"error":"connection error: desc = \"
        //  transport: authentication handshake failed: tls:
        // either ServerName or InsecureSkipVerify must be specified in the tls.Config\
        //"","code":13
        //}
        $client = new Client([
            'base_uri' => "https://lightningsale:lightningsale@lnd:8080",
            'verify' => __DIR__ . '/../../var/lnd/tls.cert',
        ]);
        $lightning = new LndClient($client);
        $paymentRequest = new SendRequest("02f220b5f4915661f004f9015496e2a7b38c123af1021390d5d2501831830304f9", "5000", "45bd4c38359e8bac1de6dbd124aa3532c7e326c5080fc800660fec24f5bc8e9d");
        dump($lightning->sendPaymentSync($paymentRequest));

    }

}