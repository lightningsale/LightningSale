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

class Channels extends Command
{
    public function __construct()
    {
        parent::__construct("app:channels");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client([
            'base_uri' => "https://lightningsale:lightningsale@lnd:8080",
            'verify' => __DIR__ . '/../../var/lnd/tls.cert',
        ]);
        $lightning = new LndClient($client);
        dump($lightning->listChannels());
    }

}