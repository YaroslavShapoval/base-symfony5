<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

abstract class BaseLocaleSubscriber implements EventSubscriberInterface
{
    protected string $defaultLocale;

    public function __construct($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    protected function processKernelRequest(RequestEvent $event, $localeSessionParam = '_locale')
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // try to see if the locale has been set as a routing parameter
        if ($locale = $request->attributes->get($localeSessionParam)) {
            $request->getSession()->set($localeSessionParam, $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getSession()->get($localeSessionParam, $this->defaultLocale));
        }
    }
}