<?php


namespace App\Security;


use App\Entity\User;
use App\Security\Exception\BlockedByAdminException;
use App\Security\Exception\DeletedByUserException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @inheritDoc
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->getIsDeletedByUser()) {
            throw new DeletedByUserException();
        }
    }

    /**
     * @inheritDoc
     */
    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->getIsBlockedByAdmin()) {
            throw new BlockedByAdminException();
        }
    }
}