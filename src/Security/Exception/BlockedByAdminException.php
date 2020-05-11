<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\LockedException;

class BlockedByAdminException extends LockedException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Account is blocked. Please contact with administrator.';
    }
}