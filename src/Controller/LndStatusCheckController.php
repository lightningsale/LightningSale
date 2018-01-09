<?php
/**
 * Created by PhpStorm.
 * User: richa
 * Date: 09.01.2018
 * Time: 21:04
 */

namespace App\Controller;


use LightningSale\LndRest\Resource\LndClient;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LndStatusCheckController implements EventSubscriberInterface {

    private $lndClient;
    private $engine;

    public function __construct(LndClient $lndClient, \Twig_Environment $engine)
    {
        $this->lndClient = $lndClient;
        $this->engine = $engine;
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

        try {
            $info = $this->lndClient->getInfo();
        } catch (\Exception $exception) {
            $event->setResponse($this->couldNotConnect($exception));
            return;
        }

        $event->stopPropagation();
        return;
    }

    private function couldNotConnect(\Exception $exception): Response
    {
        dump($exception);

        return $this->render("LndStatus/error.html.twig", ['message' => $exception->getMessage()]);
    }

    private function render(string $template, array $parameters = []): Response
    {
        $content = $this->engine->render($template, $parameters);

        $response = new Response();
        $response->setContent($content);
        return $response;
    }
}