<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 28.12.17
 * Time: 21:50
 */

namespace App\Controller;


use App\Form\Config\ConfigDTO;
use App\Form\Config\ConfigType;
use App\Form\Config\NewChannelType;
use App\Form\Config\WithdrawFundsType;
use App\Repository\ConfigRepository;
use App\Service\Twig\SatoshiConverter;
use Doctrine\ORM\EntityManagerInterface;
use LightningSale\LndClient\Client as LndRestClient;
use LightningSale\LndClient\Model\ActiveChannel;
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

    public function __construct(LndRestClient $lndClient)
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
            // "App\Exchange\BitmyntNo::NOK"
            [$exchange, $currency] = explode("::", $newValue);
            $configRepo->getConfig(ConfigRepository::CURRENCY)->updateValue($currency);
            $configRepo->getConfig(ConfigRepository::EXCHANGE)->updateValue($exchange);
            $configRepo->getConfig(ConfigRepository::LOCALE)->updateValue($configDto->locale);
            $configRepo->getConfig(ConfigRepository::INVOICE_TIMEOUT)->updateValue($configDto->invoice_timeout);

            $em->flush();
            $this->addFlash("success", "Changes saved");
            return $this->redirectToRoute("settings_config");
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
            $data = $withdrawForm->getData();
            $addr = $data['address'];
            $amount = $data['amount'];
            $satPrByte = $data['fee'];
            $txid = $this->lndClient->sendCoins($addr, $amount, null, $satPrByte);
            $this->addFlash("success", "Funds have been sent with transaction id: $txid");
            $this->redirectToRoute("settings_wallet");
        }

        foreach ($withdrawForm->getErrors() as $error) {
            $this->addFlash("warning", $error->getMessage());
        }

        return $this->redirectToRoute("settings_wallet");
    }
}