<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 28.12.17
 * Time: 21:50
 */

namespace App\Controller;


use App\Form\ConfigDTO;
use App\Form\ConfigType;
use App\Form\NewChannelType;
use App\Form\WithdrawFundsType;
use App\Repository\ConfigRepository;
use App\Service\Twig\SatoshiConverter;
use Doctrine\ORM\EntityManagerInterface;
use LightningSale\LndRest\Model\ActiveChannel;
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
     * @Route("/", name="config")
     */
    public function configAction(Request $request, ConfigRepository $configRepo, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ConfigType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ConfigDTO $configDto */
            $configDto = $form->getData();
            $newValue = $configDto->currency;
            $configRepo->getConfig(ConfigRepository::CURRENCY)->updateValue($newValue);
            $configRepo->getConfig(ConfigRepository::LOCALE)->updateValue($configDto->locale);
            $configRepo->getConfig(ConfigRepository::INVOICE_TIMEOUT)->updateValue($configDto->invoice_timeout);

            $em->flush();
        }

        return $this->render("Settings/config.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/wallet", name="wallet")
     */
    public function walletAction(SatoshiConverter $convertSatoshi): Response
    {
        $channels = $this->lndClient->listChannels();
        $pendingChannels = $this->lndClient->pendingChannels();
        $channelDatasets = ChannelsController::orderChartData($channels, $pendingChannels, $convertSatoshi);
        $wallet = $this->lndClient->walletBalance();
        $pendingInvoices = $this->lndClient->listInvoices(true);
        $newWitnessAddress = $this->lndClient->newWitnessAddress();
        $withdrawForm = $this->createForm(WithdrawFundsType::class, null, ['action' => $this->generateUrl("settings_withdraw")]);
        $newChannelForm = $this->createForm(NewChannelType::class, null, ['action' => $this->generateUrl("channels_new_channel")]);

        $balanceInChannels = array_reduce($channels, function(int $carry, ActiveChannel $channel) {return $carry + (int) $channel->getLocalBalance();}, 0);

        return $this->render("Settings/wallet.html.twig", [
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
}