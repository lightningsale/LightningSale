<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 21.12.17
 * Time: 09:44
 */

namespace App\Command;


use GuzzleHttp\Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use LightningSale\LndRest\Model\ConnectPeerRequest;
use LightningSale\LndRest\Model\LightningAddress;
use LightningSale\LndRest\Model\OpenChannelRequest;
use LightningSale\LndRest\Normalizer\NormalizerFactory;
use LightningSale\LndRest\Resource\LndClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

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
        dump($lightning->newWitnessAddress());
        exit();

        //$response = $lightning->openChannelSync();
        //$response = $lightning->connectPeer(new ConnectPeerRequest(new LightningAddress("038b869a90060ca856ac80ec54c20acebca93df1869fbee9550efeb238b964558c", "172.104.59.47:9735"), true));
        $peers = $lightning->listPeers();
        foreach ($peers->getPeers() as $peer) {
            $response = $lightning->openChannelSync(new OpenChannelRequest(
                $peer->getPeerId(),
                $peer->getPubKey(),
                1670000,
                0,
                0,
                10,
                false
            ));
            dump($response);
        }

    }

}