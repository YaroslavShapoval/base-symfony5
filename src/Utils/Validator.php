<?php

namespace App\Utils;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use function Symfony\Component\String\u;

class Validator
{
    public function validateUsername(?string $username): string
    {
        if (empty($username)) {
            throw new InvalidArgumentException('The username can not be empty.');
        }

        if (1 !== preg_match('/^[a-z_\.]+$/', $username)) {
            throw new InvalidArgumentException('The username must contain only lowercase latin characters, underscores and dots.');
        }

        if (u($username)->trim()->length() < 4) {
            throw new InvalidArgumentException('The username must be at least 4 characters long.');
        }

        if (u($username)->trim()->length() > 255) {
            throw new InvalidArgumentException('The username must be at most 255 characters long.');
        }

        return $username;
    }

    public function validatePassword(?string $plainPassword): string
    {
        if (empty($plainPassword)) {
            throw new InvalidArgumentException('The password can not be empty.');
        }

        if (u($plainPassword)->trim()->length() < 6) {
            throw new InvalidArgumentException('The password must be at least 6 characters long.');
        }

        return $plainPassword;
    }

    public function validateEmail(?string $email): string
    {
        $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (empty($sanitizedEmail)) {
            throw new InvalidArgumentException('The email can not be empty.');
        }

        if (!filter_var(u($sanitizedEmail), FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('The email address is not valid.');
        }

        return $email;
    }
}