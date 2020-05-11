<?php

// partially copied from symfony/demo

use App\Command\CreateAdminCommand;
use App\Entity\Admin;
use App\Repository\AdminRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateAdminCommandTest extends KernelTestCase
{
    private array $adminData = [
        'username' => 'admin_username',
        'password' => 'super_secure_password',
        'email' => 'admin@example.com',
    ];

    protected function setUp(): void
    {
        exec('stty 2>&1', $output, $exitcode);
        $isSttySupported = 0 === $exitcode;

        if ('Windows' === PHP_OS_FAMILY || !$isSttySupported) {
            $this->markTestSkipped('`stty` is required to test this command.');
        }
    }

    /**
     * @dataProvider isSuperAdminDataProvider
     *
     * This test provides all the arguments required by the command, so the
     * command runs non-interactively and it won't ask for any argument.
     * @param bool $isSuperAdmin
     */
    public function testCreateAdminNonInteractive(bool $isSuperAdmin): void
    {
        $input = $this->adminData;
        if ($isSuperAdmin) {
            $input['--super-admin'] = 1;
        }
        $this->executeCommand($input);

        $this->assertAdminCreated($isSuperAdmin);
    }

    /**
     * @dataProvider isSuperAdminDataProvider
     *
     * This test doesn't provide all the arguments required by the command, so
     * the command runs interactively and it will ask for the value of the missing
     * arguments.
     * See https://symfony.com/doc/current/components/console/helpers/questionhelper.html#testing-a-command-that-expects-input
     * @param bool $isSuperAdmin
     */
    public function testCreateAdminInteractive(bool $isSuperAdmin): void
    {
        $this->executeCommand(
        // these are the arguments (only 1 is passed, the rest are missing)
            $isSuperAdmin ? ['--super-admin' => 1] : [],
            // these are the responses given to the questions asked by the command
            // to get the value of the missing required arguments
            array_values($this->adminData)
        );

        $this->assertAdminCreated($isSuperAdmin);
    }

    /**
     * This is used to execute the same test twice: first for normal admins
     * (isSuperAdmin = false) and then for super admins (isSuperAdmin = true).
     */
    public function isSuperAdminDataProvider(): ?Generator
    {
        yield [false];
        yield [true];
    }

    /**
     * This helper method checks that the admin was correctly created and saved in the database.
     * @param bool $isSuperAdmin
     */
    private function assertAdminCreated(bool $isSuperAdmin): void
    {
        $container = self::$container;

        /** @var Admin $admin */
        $admin = $container->get(AdminRepository::class)->findOneByEmail($this->adminData['email']);
        $this->assertNotNull($admin);

        $this->assertSame($this->adminData['username'], $admin->getUsername());
        $this->assertTrue($container->get('security.password_encoder')->isPasswordValid($admin, $this->adminData['password']));
        $this->assertSame($isSuperAdmin ? ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN'] : ['ROLE_ADMIN'], $admin->getRoles());
    }

    /**
     * This helper method abstracts the boilerplate code needed to test the
     * execution of a command.
     *
     * @param array $arguments All the arguments passed when executing the command
     * @param array $inputs    The (optional) answers given to the command when it asks for the value of the missing arguments
     */
    private function executeCommand(array $arguments, array $inputs = []): void
    {
        self::bootKernel();

        // this uses a special testing container that allows you to fetch private services
        /** @var CreateAdminCommand $command */
        $command = self::$container->get(CreateAdminCommand::class);
        $command->setApplication(new Application(self::$kernel));

        $commandTester = new CommandTester($command);
        $commandTester->setInputs($inputs);
        $commandTester->execute($arguments);
    }
}