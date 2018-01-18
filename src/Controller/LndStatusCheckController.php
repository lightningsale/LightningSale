<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: richa
 * Date: 09.01.2018
 * Time: 21:04
 */

namespace App\Controller;


use GuzzleHttp\Exception\ConnectException;
use LightningSale\LndClient\LndException;
use LightningSale\LndClient\RestClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LndStatusCheckController implements EventSubscriberInterface {

    private $lndClient;
    private $engine;
    private $logger;

    public function __construct(
        RestClient $lndClient,
        \Twig_Environment $engine,
        LoggerInterface $logger
    ){
        $this->lndClient = $lndClient;
        $this->engine = $engine;
        $this->logger = $logger;
    }


    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

    public function onKernelRequest(GetResponseEvent  $event): void
    {
        if (!$event->isMasterRequest())
            return;

        $pathInfo = $event->getRequest()->getPathInfo();
        if (strpos($pathInfo, "/_wdt") === 0
            || strpos($pathInfo, "/_profile") === 0)
            return;

        try {
            $info = $this->lndClient->getInfo();

            if (!$info->isSyncedToChain())
                $this->returnAndRenderMessage($event, "LND is Syncing with the Blockchain, please try again later (aprox. 10 - 15 minutes)");

        } catch (LndException $exception) {
            $this->logger->critical("LndException", $exception);

            if ($exception->getCode() === 404)
                $this->returnAndRenderMessage($event, " Waiting for wallet encryption password. Use `lncli create` to create wallet, or `lncli unlock` to unlock already created wallet.", "warning");
            else
                throw $exception;

        } catch (ConnectException $exception) {
            $this->logger->critical("Connection error", $exception);
            $this->returnAndRenderMessage($event, "Could not connect to LND, make sure it is running!", "danger");
        } catch (\Exception $exception) {
            $this->logger->critical("Unkown error", $exception);
            $this->returnAndRenderMessage($event, $exception->getMessage(), "danger");
        }
    }

    private function returnAndRenderMessage(GetResponseEvent $event, string $message, string $type = "info"): void
    {
        $response = $this->render("LndStatus/message.html.twig", ['message' => $message, 'type' => $type]);
        $event->setResponse($response);
        $event->stopPropagation();
    }

    private function render(string $template, array $parameters = []): Response
    {
        $content = $this->engine->render($template, $parameters);

        $response = new Response();
        $response->setContent($content);
        return $response;
    }

    private function createOrUnlockWallet(GetResponseEvent $event): void
    {
        //TODO: Fix this event
        // wallet password must be minimum 8 characters
        // https://github.com/lightningnetwork/lnd/issues/579

        $response = new Response("Unlock or create wallet!");
        $event->setResponse($response);
        $event->stopPropagation();
    }
}