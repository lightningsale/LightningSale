<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 28.12.17
 * Time: 21:50
 */

namespace App\Controller;


use LightningSale\LndRest\Resource\LndClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SettingsController
 * @package App\Controller
 * @Route("/dashboard/settings", name="settings_")
 */
class SettingsController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(LndClient $lndClient): Response
    {
        $peers = $lndClient->listPeers();
        $channels = $lndClient->listChannels();
        $pendingChannels = $lndClient->pendingChannels();
        $wallet = $lndClient->walletBalance();
        $pendingInvoices = $lndClient->listInvoices(true);

        return $this->render("Settings/index.html.twig", [
            'peers'=> $peers,
            'channels'=> $channels,
            'pendingChannels'=> $pendingChannels,
            'wallet' => $wallet,
            'pendingInvoices' => $pendingInvoices,
        ]);
    }
}