<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testBookcaseCreatedForUser()
    {
        /** @var UserRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        $email = 'some.email@example.com';

        // Check that user with this email is not in the db
        $this->assertEmpty($userRepository->findOneBy(['email' => $email]));

        // Create new user and save it
        $user = new User($email);
        $user->setPassword('123');

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (ORMException $e) {}

        // Check that this user successfully stored to the db
        $this->assertNotEmpty($userRepository->findOneBy(['email' => $email]));

        // Check that this user has connected bookcase created
        $this->assertNotEmpty($user->getBookcase());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
