<?php

namespace App\Tests\Controller;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private const VALID_USER_EMAIL = 'valid_user@example.com';
    private const VALID_USER_PASSWORD = 'valid_password';
    private const NON_VALID_USERS_PASSWORD = 'password';

    /**
     * @dataProvider getRightCredentials
     * @param $email
     * @param $password
     */
    public function testLoginWithRightCredentials($email, $password)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');
        $form = $crawler->filter('.test-login-form')->form([
            'email' => $email,
            'password' => $password,
        ]);
        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        $this->assertEquals('http://localhost/language-test', $client->getRequest()->getUri());
        $this->assertTrue($crawler->filter('html:contains("Hello LanguageSpecificController!")')->count() > 0);
    }

    public function getRightCredentials(): ?Generator
    {
        yield ['valid_user_01@example.com', 'valid_password'];
        yield ['valid_user_02@example.com', 'valid_password'];
        yield ['valid_user_03@example.com', 'valid_password'];
    }

    /**
     * @dataProvider getWrongCredentials
     * @param $email
     * @param $password
     * @param $errorMessage
     */
    public function testLoginWithWrongCredentials($email, $password, $errorMessage)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');
        $form = $crawler->filter('.test-login-form')->form([
            'email' => $email,
            'password' => $password,
        ]);
        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();

        $this->assertEquals('http://localhost/login', $client->getRequest()->getUri());
        $this->assertTrue($crawler->filter('html:contains("'.$errorMessage.'")')->count() > 0);
    }

    public function getWrongCredentials(): ?Generator
    {
        yield ['', '', 'Invalid credentials.'];
        yield ['e@mail', '', 'Invalid credentials.'];
        yield [self::VALID_USER_EMAIL, '', 'Invalid credentials.'];
        yield ['', self::VALID_USER_PASSWORD, 'Invalid credentials.'];
        yield ['', self::VALID_USER_EMAIL, 'Invalid credentials.'];
        yield [str_replace('@', '', self::VALID_USER_EMAIL), self::VALID_USER_PASSWORD, 'User with these credentials could not be found.'];
        yield [str_replace('.', '', self::VALID_USER_EMAIL), self::VALID_USER_PASSWORD, 'User with these credentials could not be found.'];
        yield [self::VALID_USER_EMAIL, 'wrong_password', 'Invalid credentials.'];
        yield [self::VALID_USER_EMAIL, self::VALID_USER_EMAIL, 'Invalid credentials.'];
        yield [self::VALID_USER_PASSWORD, self::VALID_USER_PASSWORD, 'User with these credentials could not be found.'];

        yield ['blocked_user@example.com', '', 'Invalid credentials.'];
        yield ['blocked_user@example.com', 'wrong_password', 'Invalid credentials.'];
        yield ['blocked_user@example.com', self::NON_VALID_USERS_PASSWORD, 'Account is blocked. Please contact with administrator.'];

        yield ['deleted_user@example.com', '', 'Invalid credentials.'];
        yield ['deleted_user@example.com', 'wrong_password', 'User with these credentials could not be found.'];
        yield ['deleted_user@example.com', self::NON_VALID_USERS_PASSWORD, 'User with these credentials could not be found.'];
    }
}
