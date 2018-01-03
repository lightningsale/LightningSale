<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 29.12.17
 * Time: 17:01
 */

namespace App\Controller;


use App\Service\Twig\ConvertSatoshi;
use LightningSale\LndRest\Model\ActiveChannel;
use LightningSale\LndRest\Model\PendingChannelResponse;
use LightningSale\LndRest\Model\PendingChannelResponseClosedChannel;
use LightningSale\LndRest\Model\PendingChannelResponseForceClosedChannel;
use LightningSale\LndRest\Model\PendingChannelResponsePendingOpenChannel;
use LightningSale\LndRest\Resource\LndClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    public function __construct(ConvertSatoshi $convertSatoshi, LndClient $lndClient)
    {
        $this->convertSatoshi = $convertSatoshi;
        $this->lndClient = $lndClient;
    }


    /**
     * @Route("/", name="index")
     */
    public function indexAction(): Response
    {
        $channels = $this->lndClient->listChannels();
        $pendingChannels = $this->lndClient->pendingChannels();
        $channelDatasets = self::orderChartData($channels, $pendingChannels, $this->convertSatoshi);

        return $this->render("Channels/index.html.twig", [
            'openChannels' => $channels,
            'pendingChannels' => $pendingChannels,
            'channelDatasets' => $channelDatasets,
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
     * @param ActiveChannel[] $channels
     * @param PendingChannelResponse $pendingChannels
     * @return array
     */
    public static function orderChartData(array $channels, PendingChannelResponse $pendingChannels, ConvertSatoshi $convertSatoshi): array
    {
        return [
            'local' => array_merge(
                array_map(function (ActiveChannel $ac) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToMilliBtc($ac->getLocalBalance());
                }, $channels),
                array_map(function (PendingChannelResponsePendingOpenChannel $po) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToMilliBtc($po->getChannel()->getLocalBalance());
                }, $pendingChannels->getPendingOpenChannels()),
                array_map(function (PendingChannelResponseClosedChannel $pc) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToMilliBtc($pc->getChannel()->getLocalBalance());
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToMilliBtc($pf->getChannel()->getLocalBalance());
                }, $pendingChannels->getPendingForceClosingChannels())
            ),
            'remote' => array_merge(
                array_map(function (ActiveChannel $ac) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToMilliBtc($ac->getRemoteBalance());
                }, $channels),
                array_map(function (PendingChannelResponsePendingOpenChannel $po) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToMilliBtc($po->getChannel()->getRemoteBalance());
                }, $pendingChannels->getPendingOpenChannels()),
                array_map(function (PendingChannelResponseClosedChannel $pc) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToMilliBtc($pc->getChannel()->getRemoteBalance());
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) use($convertSatoshi) {
                    return $convertSatoshi->satoshiToMilliBtc($pf->getChannel()->getRemoteBalance());
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
                    return "rgba(255, 205, 86, 0.2)";
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) {
                    return "rgba(201, 203, 207, 0.2)";
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
                    return "rgb(255, 205, 86)";
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) {
                    return "rgb(201, 203, 207)";
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
                    return "rgba(75, 192, 192, 0.2)";
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) {
                    return "rgba(201, 203, 207, 0.2)";
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
                    return "rgb(201, 203, 207)";
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) {
                    return "rgb(75, 192, 192)";
                }, $pendingChannels->getPendingForceClosingChannels())
            )
        ];
    }

}