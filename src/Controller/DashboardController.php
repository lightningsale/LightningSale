<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 23.12.17
 * Time: 12:52
 */

namespace App\Controller;

use App\Entity\Cashier;
use App\Form\Config\NewInvoiceType;
use App\Repository\ConfigRepository;
use App\Service\Twig\SatoshiConverter;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\QrCode;
use LightningSale\LndClient\Client as LndRestClient;
use LightningSale\LndClient\Model\Invoice;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DashboardController
 * @package App\Controller\Cashier
 * @Route("/dashboard", name="cashier_dashboard_")
 * @Security("is_granted('ROLE_CASHIER')")
 */
class DashboardController extends Controller
{
    private $lndClient;
    private $em;

    public function __construct(LndRestClient $lndClient, EntityManagerInterface $em)
    {
        $this->lndClient = $lndClient;
        $this->em = $em;
    }


    /**
     * @Route("/", name="index")
     */
    public function indexAction(): Response
    {
        $form = $this->createForm(NewInvoiceType::class,null, [
            'action' => $this->generateUrl("cashier_dashboard_new_invoice")
        ]);

        $invoices = $this->lndClient->listInvoices(true);
        $invoices = array_filter($invoices, function(Invoice $invoice) {
            return new \DateTime("-1 days") < $invoice->getExpiry();
        });
        usort($invoices, function (Invoice $a, Invoice $b) {
            return $b->getCreationDate() <=> $a->getCreationDate();
        });

        return $this->render("Dashboard/dashboard.html.twig", [
            'invoices' => $invoices,
            'form' => $form->createView(),
            'now' => new \DateTime(),
        ]);
    }

    /**
     * @Route("/new_invoice", name="new_invoice")
     * @param Cashier $user
     */
    public function createInvoiceAction(Request $request, UserInterface $user, SatoshiConverter $convertSatoshi, ConfigRepository $configRepository): Response
    {
        $form = $this->createForm(NewInvoiceType::class);
        $form->handleRequest($request);
        $data=$form->getData();

        $localAmount = (float) $data['amount'];
        $amount = (string) $convertSatoshi->localToSatoshi($localAmount); // Extremely buggy ?!
        $timeoutConfig = $configRepository->getConfig(ConfigRepository::INVOICE_TIMEOUT);
        $formatSatoshi = $convertSatoshi->formatSatoshi($amount);

        $user->createInvoice(
            $this->lndClient,
            $amount,
            sprintf("LightningSale %s", $formatSatoshi),
            (int) $timeoutConfig->getValue()
        );

        $this->em->flush();

        return $this->redirectToRoute("cashier_dashboard_index");
    }

    /**
     * @Route("/explorer/{txId}", name="explorer")
     */
    public function transactionAction(string $txId, LndRestClient $lndClient): Response
    {
        $testnet = $lndClient->getInfo()->isTestnet();
        if ($testnet)
            return new RedirectResponse(sprintf("https://www.blocktrail.com/tBTC/tx/%s", $txId));

        return new RedirectResponse(sprintf("https://www.blocktrail.com/BTC/tx/%s", $txId));
    }

    /**
     * @Route("/qrcode/{message}", requirements={"message": ".+"})
     */
    public function createQrCodeAction(string $message): Response
    {
        $qrCode = new QrCode($message);
        $qrCode->setSize(1000);

        return new Response($qrCode->writeString(),200, ['Content-Type' => $qrCode->getContentType()]);
    }
}