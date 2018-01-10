<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 29.12.17
 * Time: 17:01
 */

namespace App\Controller;


use App\Form\NewChannelType;
use App\Form\NewConnectionType;
use App\Service\Twig\SatoshiConverter;
use LightningSale\LndRest\Model\ActiveChannel;
use LightningSale\LndRest\Model\Peer;
use LightningSale\LndRest\Model\PendingChannelResponse;
use LightningSale\LndRest\Model\PendingChannelResponseClosedChannel;
use LightningSale\LndRest\Model\PendingChannelResponseForceClosedChannel;
use LightningSale\LndRest\Model\PendingChannelResponsePendingOpenChannel;
use LightningSale\LndRest\LndClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChannelsController
 * @package App\Controller
 * @Route("/dashboard/settings/channels", name="channels_")
 * @Security("is_granted('ROLE_MERCHANT')")
 */
class ChannelsController extends Controller
{

    private $convertSatoshi;
    private $lndClient;

    public function __construct(SatoshiConverter $convertSatoshi, LndClient $lndClient)
    {
        $this->convertSatoshi = $convertSatoshi;
        $this->lndClient = $lndClient;
    }


    /**
     * @Route("/", name="index")
     */
    public function indexAction(): Response
    {
        $peers = $this->lndClient->listPeers();
        $channels = $this->lndClient->listChannels();
        $pendingChannels = $this->lndClient->pendingChannels();
        $channelDatasets = self::orderChartData($channels, $pendingChannels, $this->convertSatoshi);
        $newChannelForm = $this->createForm(NewChannelType::class, null, ['action' => $this->generateUrl("channels_new_channel")]);
        $newConnectionForm = $this->createForm(NewConnectionType::class, null, ['action' => $this->generateUrl("channels_connection_new")]);

        $activePeers = [];
        foreach ($channels as $channel)
            $activePeers[$channel->getRemotePubkey()] = true;

        foreach ($pendingChannels->getPendingOpenChannels() as $pendingChannel)
            $activePeers[$pendingChannel->getChannel()->getRemotePubkey()] = true;

        foreach ($pendingChannels->getPendingClosingChannels() as $pendingChannel)
            $activePeers[$pendingChannel->getChannel()->getRemotePubkey()] = true;

        foreach ($pendingChannels->getPendingForceClosingChannels() as $pendingChannel)
            $activePeers[$pendingChannel->getChannel()->getRemotePubkey()] = true;

        /** @var Peer[] $inactivePeers */
        $inactivePeers = [];
        foreach ($peers as $peer)
            if (!isset($activePeers[$peer->getPubKey()]))
                $inactivePeers[] = $peer;

        return $this->render("Channels/channels.html.twig", [
            'openChannels' => $channels,
            'pendingChannels' => $pendingChannels,
            'channelDatasets' => $channelDatasets,
            'inactivePeers'   => $inactivePeers,
            'newChannelForm'  => $newChannelForm->createView(),
            'newConnectionForm'  => $newConnectionForm->createView(),
        ]);
    }

    /**
     * @Route("/close/{id}/{index}/{force}", name="close", defaults={"force": 0})
     */
    public function closeAction($id, $index, $force = 0): Response
    {
        $this->lndClient->closeChannel($id, $index, (bool) $force);

        $this->addFlash("success", "Channel closed");
        return $this->redirectToRoute("channels_index");
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
                return $this->redirectToRoute("channels_index");
            }

            $txid = $this->lndClient->openChannelSync($pubKey, (string) $data['amount']);

            $this->addFlash("success", "New channel opened (txid: $txid)");
            $this->redirectToRoute("channels_index");
        }

        foreach ($form->getErrors() as $error) {
            $this->addFlash("warning", $error->getMessage());
        }

        return $this->redirectToRoute("channels_index");
    }

    /**
     * @Route("/new_connection", name="connection_new")
     */
    public function newConnectionAction(Request $request) {
        $form = $this->createForm(NewChannelType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $pubKey = $data['pubkey'];
            $host = $data['host'];

            $this->lndClient->connectPeer($pubKey, $host, true);

            $this->addFlash("success", "New channel opened with $host");
            $this->redirectToRoute("channels_index");
        }

        foreach ($form->getErrors() as $error) {
            $this->addFlash("warning", $error->getMessage());
        }

        return $this->redirectToRoute("channels_index");
    }

    /**
     * @param ActiveChannel[] $channels
     * @param PendingChannelResponse $pendingChannels
     * @return array
     */
    public static function orderChartData(array $channels, PendingChannelResponse $pendingChannels, SatoshiConverter $convertSatoshi): array
    {
        return [
            'local' => array_merge(
                array_map(function (ActiveChannel $ac) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToLocal($ac->getLocalBalance());
                }, $channels),
                array_map(function (PendingChannelResponsePendingOpenChannel $po) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToLocal($po->getChannel()->getLocalBalance());
                }, $pendingChannels->getPendingOpenChannels()),
                array_map(function (PendingChannelResponseClosedChannel $pc) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToLocal($pc->getChannel()->getLocalBalance());
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToLocal($pf->getChannel()->getLocalBalance());
                }, $pendingChannels->getPendingForceClosingChannels())
            ),
            'remote' => array_merge(
                array_map(function (ActiveChannel $ac) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToLocal($ac->getRemoteBalance());
                }, $channels),
                array_map(function (PendingChannelResponsePendingOpenChannel $po) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToLocal($po->getChannel()->getRemoteBalance());
                }, $pendingChannels->getPendingOpenChannels()),
                array_map(function (PendingChannelResponseClosedChannel $pc) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToLocal($pc->getChannel()->getRemoteBalance());
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToLocal($pf->getChannel()->getRemoteBalance());
                }, $pendingChannels->getPendingForceClosingChannels())
            ),
            'labels' => array_merge(
                array_map(function (ActiveChannel $ac) {
                    return "Open";
                }, $channels),
                array_map(function (PendingChannelResponsePendingOpenChannel $po) {
                    return "Pending Open";
                }, $pendingChannels->getPendingOpenChannels()),
                array_map(function (PendingChannelResponseClosedChannel $pc) {
                    return "Pending Close";
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) {
                    return "Pending Force Close";
                }, $pendingChannels->getPendingForceClosingChannels())
            ),
            'local_background' => array_merge(
                array_map(function (ActiveChannel $ac) {
                    return "rgba(255, 205, 86, 0.2)";
                }, $channels),
                array_map(function (PendingChannelResponsePendingOpenChannel $po) {
                    return "rgba(153, 102, 255, 0.2)";
                }, $pendingChannels->getPendingOpenChannels()),
                array_map(function (PendingChannelResponseClosedChannel $pc) {
                    return "rgba(153, 102, 255, 0.2)";
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) {
                    return "rgba(153, 102, 255, 0.2)";
                }, $pendingChannels->getPendingForceClosingChannels())
            ),
            'local_border' => array_merge(
                array_map(function (ActiveChannel $ac) {
                    return "rgb(255, 205, 86)";
                }, $channels),
                array_map(function (PendingChannelResponsePendingOpenChannel $po) {
                    return "rgb(153, 102, 255)";
                }, $pendingChannels->getPendingOpenChannels()),
                array_map(function (PendingChannelResponseClosedChannel $pc) {
                    return "rgb(153, 102, 255)";
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) {
                    return "rgb(153, 102, 255)";
                }, $pendingChannels->getPendingForceClosingChannels())
            ),
            'remote_background' => array_merge(
                array_map(function (ActiveChannel $ac) {
                    return "rgba(75, 192, 192, 0.2)";
                }, $channels),
                array_map(function (PendingChannelResponsePendingOpenChannel $po) {
                    return "rgba(153, 102, 255, 0.2)";
                }, $pendingChannels->getPendingOpenChannels()),
                array_map(function (PendingChannelResponseClosedChannel $pc) {
                    return "rgba(153, 102, 255, 0.2)";
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) {
                    return "rgba(153, 102, 255, 0.2)";
                }, $pendingChannels->getPendingForceClosingChannels())
            ),
            'remote_border' => array_merge(
                array_map(function (ActiveChannel $ac) {
                    return "rgb(75, 192, 192)";
                }, $channels),
                array_map(function (PendingChannelResponsePendingOpenChannel $po) {
                    return "rgb(153, 102, 255)";
                }, $pendingChannels->getPendingOpenChannels()),
                array_map(function (PendingChannelResponseClosedChannel $pc) {
                    return "rgb(153, 102, 255)";
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) {
                    return "rgb(153, 102, 255)";
                }, $pendingChannels->getPendingForceClosingChannels())
            )
        ];
    }

    private function findPeer($pubKey): ?Peer
    {
        $peers = $this->lndClient->listPeers();
        foreach ($peers as $p)
            if ($p->getPubKey() === $pubKey)
                return $p;

        return null;
    }
}