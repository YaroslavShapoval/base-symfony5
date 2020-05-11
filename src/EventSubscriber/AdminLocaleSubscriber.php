<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AdminLocaleSubscriber extends BaseLocaleSubscriber
{
    public function onKernelRequest(RequestEvent $event)
    {
        parent::processKernelRequest($event, $localeSessionParam = AdminUserLocaleSubscriber::LOCALE_SESSION_PARAM);
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}