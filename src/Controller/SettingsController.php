<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 28.12.17
 * Time: 21:50
 */

namespace App\Controller;


use App\Form\NewChannelType;
use App\Form\WithdrawFundsType;
use App\Service\Twig\ConvertSatoshi;
use LightningSale\LndRest\Model\ActiveChannel;
use LightningSale\LndRest\Model\Peer;
use LightningSale\LndRest\Resource\LndClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SettingsController
 * @package App\Controller
 * @Route("/dashboard/settings", name="settings_")
 * @Security("is_granted('ROLE_MERCHANT')")
 */
class SettingsController extends Controller
{
    private $lndClient;

    public function __construct(LndClient $lndClient)
    {
        $this->lndClient = $lndClient;
    }


    /**
     * @Route("/", name="index")
     */
    public function indexAction(ConvertSatoshi $convertSatoshi): Response
    {
        $channels = $this->lndClient->listChannels();
        $pendingChannels = $this->lndClient->pendingChannels();
        $channelDatasets = ChannelsController::orderChartData($channels, $pendingChannels, $convertSatoshi);
        $wallet = $this->lndClient->walletBalance();
        $pendingInvoices = $this->lndClient->listInvoices(true);
        $newWitnessAddress = $this->lndClient->newWitnessAddress();
        $withdrawForm = $this->createForm(WithdrawFundsType::class, null, ['action' => $this->generateUrl("settings_withdraw")]);
        $newChannelForm = $this->createForm(NewChannelType::class, null, ['action' => $this->generateUrl("settings_new_channel")]);

        $balanceInChannels = array_reduce($channels, function(int $carry, ActiveChannel $channel) {return $carry + (int) $channel->getLocalBalance();}, 0);

        return $this->render("Settings/index.html.twig", [
            'channels'=> $channels,
            'pendingChannels'=> $pendingChannels,
            'wallet' => $wallet,
            'pendingInvoices' => $pendingInvoices,
            'channelBalance' => $balanceInChannels,
            'channelDatasets' => $channelDatasets,
            'newAddress' => $newWitnessAddress,
            'withdrawForm' => $withdrawForm->createView(),
            'newChannelForm' => $newChannelForm->createView(),
        ]);
    }

    /**
     * @Route("/withdraw_funds", name="withdraw")
     */
    public function withdrawAction(Request $request) {
        $withdrawForm = $this->createForm(WithdrawFundsType::class);
        $withdrawForm->handleRequest($request);

        if ($withdrawForm->isSubmitted() && $withdrawForm->isValid()) {
            $txid = $this->lndClient->sendCoins($withdrawForm->getData());
            $this->addFlash("success", "Funds have been sent with transaction id: $txid");
            $this->redirectToRoute("settings_index");
        }

        foreach ($withdrawForm->getErrors() as $error) {
            $this->addFlash("warning", $error->getMessage());
        }

        return $this->redirectToRoute("settings_index");
    }

    /**
     * @Route("/new_channel", name="new_channel")
     */
    public function newChannelAction(Request $request) {
        $form = $this->createForm(NewChannelType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $pubKey = $data['pubkey'];
            $host = $data['host'];
            $peer = $this->findPeer($pubKey);
            if (!$peer)
                $this->lndClient->connectPeer($pubKey, $host, true);

            $timeout = 60;
            while ($timeout>0 && !$peer){
                $peer = $this->findPeer($pubKey);
                if (!$peer)
                    sleep(1);
            }
            if (!$peer) {
                $this->addFlash("warning", "Can't connect to peer!");
                return $this->redirectToRoute("settings_index");
            }

            $txid = $this->lndClient->openChannelSync($pubKey, $data['amount']);

            $this->addFlash("success", "New channel opened (txid: $txid)");
            $this->redirectToRoute("settings_index");
        }

        foreach ($form->getErrors() as $error) {
            $this->addFlash("warning", $error->getMessage());
        }

        return $this->redirectToRoute("settings_index");
    }

    private function findPeer($pubKey): ?Peer
    {
        $peers = $this->lndClient->listPeers();
        dump($peers);
        foreach ($peers as $p)
            if ($p->getPubKey() === $pubKey)
                return $p;

        return null;
    }

}