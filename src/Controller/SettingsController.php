<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 28.12.17
 * Time: 21:50
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
        $channels = $lndClient->listChannels();
        $pendingChannels = $lndClient->pendingChannels();
        $wallet = $lndClient->walletBalance();
        $pendingInvoices = $lndClient->listInvoices(true);
        $channelDatasets = $this->orderChartData($channels, $pendingChannels);

        $balanceInChannels = array_reduce($channels, function(int $carry, ActiveChannel $channel) {return $carry + (int) $channel->getLocalBalance();}, 0);

        return $this->render("Settings/index.html.twig", [
            'channels'=> $channels,
            'pendingChannels'=> $pendingChannels,
            'wallet' => $wallet,
            'pendingInvoices' => $pendingInvoices,
            'channelBalance' => $balanceInChannels,
            'channelDatasets' => $channelDatasets
        ]);
    }

    /**
     * @param ActiveChannel[] $channels
     * @param PendingChannelResponse $pendingChannels
     * @return array
     */
    private function orderChartData(array $channels, PendingChannelResponse $pendingChannels): array
    {
        return [
            'local' => array_merge(
                array_map(function (ActiveChannel $ac) {
                    return ConvertSatoshi::satoshiToMilliBtc($ac->getLocalBalance());
                }, $channels),
                array_map(function (PendingChannelResponsePendingOpenChannel $po) {
                    return ConvertSatoshi::satoshiToMilliBtc($po->getChannel()->getLocalBalance());
                }, $pendingChannels->getPendingOpenChannels()),
                array_map(function (PendingChannelResponseClosedChannel $pc) {
                    return ConvertSatoshi::satoshiToMilliBtc($pc->getChannel()->getLocalBalance());
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) {
                    return ConvertSatoshi::satoshiToMilliBtc($pf->getChannel()->getLocalBalance());
                }, $pendingChannels->getPendingForceClosingChannels())
            ),
            'remote' => array_merge(
                array_map(function (ActiveChannel $ac) {
                    return ConvertSatoshi::satoshiToMilliBtc($ac->getRemoteBalance());
                }, $channels),
                array_map(function (PendingChannelResponsePendingOpenChannel $po) {
                    return ConvertSatoshi::satoshiToMilliBtc($po->getChannel()->getRemoteBalance());
                }, $pendingChannels->getPendingOpenChannels()),
                array_map(function (PendingChannelResponseClosedChannel $pc) {
                    return ConvertSatoshi::satoshiToMilliBtc($pc->getChannel()->getRemoteBalance());
                }, $pendingChannels->getPendingClosingChannels()),
                array_map(function (PendingChannelResponseForceClosedChannel $pf) {
                    return ConvertSatoshi::satoshiToMilliBtc($pf->getChannel()->getRemoteBalance());
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