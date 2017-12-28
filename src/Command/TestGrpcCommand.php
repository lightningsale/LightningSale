<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 21.12.17
 * Time: 09:44
 */

namespace App\Command;


use GuzzleHttp\Client;
use LightningSale\LndRest\Resource\LndClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestGrpcCommand extends Command
{
    public function __construct()
    {
        parent::__construct("app:test");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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
        dump($lightning->addInvoice("none", 50000));

    }

}