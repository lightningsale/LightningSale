<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 04.01.18
 * Time: 17:03
 */

namespace App\Service;


use App\Repository\ConfigRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleManager implements EventSubscriberInterface
{

    private $configRepo;

    /**
     * LocaleManager constructor.
     * @param $configRepo
     */
    public function __construct(ConfigRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }


    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ["onKernelRequest"],
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $config = $this->configRepo->getConfig(ConfigRepository::LOCALE, "en_US.utf8");
        $request->setLocale($config->getValue());
    }
}