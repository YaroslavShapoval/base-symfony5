<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\DisabledException;

class DeletedByUserException extends DisabledException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'User with these credentials could not be found.';
    }
}