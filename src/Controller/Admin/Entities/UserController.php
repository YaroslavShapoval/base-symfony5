<?php

namespace App\Controller\Admin\Entities;

use App\Controller\Admin\BaseAdminController;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends BaseAdminController
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function persistEntity($entity)
    {
        $encodedPassword = $this->getEncodedPassword($entity, $entity->getPlainPassword());
        if ($encodedPassword) {
            $entity->setPassword($encodedPassword);
        }

        parent::persistEntity($entity);
    }

    public function updateEntity($entity)
    {
        $encodedPassword = $this->getEncodedPassword($entity, $entity->getPlainPassword());
        if ($encodedPassword) {
            $entity->setPassword($encodedPassword);
        }

        parent::updateEntity($entity);
    }

    private function getEncodedPassword($user, $plainPassword)
    {
        if (!$user instanceof User || !$plainPassword) {
            return '';
        }

        return $this->passwordEncoder->encodePassword($user, $plainPassword);
    }
}