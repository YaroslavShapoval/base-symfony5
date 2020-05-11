<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Stores the locale of the user in the session after the login.
 * This can be used by the LocaleSubscriber afterwards.
 */
class UserLocaleSubscriber extends BaseUserLocaleSubscriber implements EventSubscriberInterface
{
    const LOCALE_SESSION_PARAM = '_locale';

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            if (null !== $user->getProfileLanguage()) {
                $this->session->set('_locale', $user->getProfileLanguage());
            }
        }
    }
}