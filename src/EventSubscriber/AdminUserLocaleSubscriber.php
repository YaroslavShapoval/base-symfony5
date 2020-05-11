<?php

namespace App\EventSubscriber;

use App\Entity\Admin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Stores the locale of the admin in the session after the login.
 * This can be used by the LocaleSubscriber afterwards.
 */
class AdminUserLocaleSubscriber extends BaseUserLocaleSubscriber implements EventSubscriberInterface
{
    const LOCALE_SESSION_PARAM = '_admin_locale';

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var Admin $user */
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof Admin) {
            if (null !== $user->getLanguage()) {
                $this->session->set('_locale', $user->getLanguage());
            }
        }
    }
}