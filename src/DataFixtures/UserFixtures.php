<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const FIRST_VALID_USER_REFERENCE = 'first-valid-user';
    public const SECOND_VALID_USER_REFERENCE = 'second-valid-user';
    public const THIRD_VALID_USER_REFERENCE = 'third-valid-user';
    public const BLOCKED_USER_REFERENCE = 'blocked-user';
    public const DELETED_USER_REFERENCE = 'deleted-user';

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->createValidUsers($manager);
        $this->createBlockedUser($manager);
        $this->createdDeletedUser($manager);

        $manager->flush();
    }

    private function createValidUsers(ObjectManager $manager)
    {
        $user = new User('valid_user_01@example.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'valid_password'));
        $manager->persist($user);
        $this->addReference(self::FIRST_VALID_USER_REFERENCE, $user);
        unset($user);

        $user = new User('valid_user_02@example.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'valid_password'));
        $manager->persist($user);
        $this->addReference(self::SECOND_VALID_USER_REFERENCE, $user);
        unset($user);

        $user = new User('valid_user_03@example.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'valid_password'));
        $manager->persist($user);
        $this->addReference(self::THIRD_VALID_USER_REFERENCE, $user);
        unset($user);
    }

    private function createBlockedUser(ObjectManager $manager)
    {
        $user = new User('blocked_user@example.com');
        $user->setIsBlockedByAdmin(true);
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
        $manager->persist($user);
        $this->addReference(self::BLOCKED_USER_REFERENCE, $user);
        unset($user);
    }

    private function createdDeletedUser(ObjectManager $manager)
    {
        $user = new User('deleted_user@example.com');
        $user->setIsDeletedByUser(true);
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
        $manager->persist($user);
        $this->addReference(self::DELETED_USER_REFERENCE, $user);
        unset($user);
    }
}
