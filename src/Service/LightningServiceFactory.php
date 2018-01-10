<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 25.12.17
 * Time: 12:59
 */

namespace App\Service;


use GuzzleHttp\Client;
use LightningSale\LndRest\LndRestClient;

class LightningServiceFactory
{
    private $lndPort;
    private $rpcPassword;
    private $lndCertFile;
    private $rpcUsername;
    private $lndHost;

    public function __construct(string $lndCertFile, string $rpcPassword, string $rpcUsername, string $lndHost, string $lndPort)
    {
        $this->lndCertFile = $lndCertFile;
        $this->rpcPassword = $rpcPassword;
        $this->rpcUsername = $rpcUsername;
        $this->lndHost = $lndHost;
        $this->lndPort = $lndPort;
    }

    public function getLndClient(): LndRestClient
    {
        $client = new Client([
            'base_uri' => "https://{$this->rpcUsername}:{$this->rpcPassword}@{$this->lndHost}:{$this->lndPort}",
            'verify' => false, // $this->lndCertFile,
        ]);

        return new LndRestClient($client);
    }
}