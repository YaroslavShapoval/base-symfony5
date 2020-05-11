<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerPrivacyTest extends WebTestCase
{
    /**
     * @dataProvider getPrivateUrls
     * @param string $httpMethod
     * @param string $url
     */
    public function testAccessDeniedForAnonymousUsers(string $httpMethod, string $url): void
    {
        $client = static::createClient();
        $client->request($httpMethod, $url);

        $this->assertResponseRedirects(
            '/login',
            Response::HTTP_FOUND,
            sprintf('The %s secure URL redirects to the login form.', $url)
        );
    }

    /**
     * @dataProvider getPrivateUrls
     * @param string $httpMethod
     * @param string $url
     */
    public function testAccessAllowedForLoggedInUsers(string $httpMethod, string $url): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@example.com',
            'PHP_AUTH_PW'   => 'user@example.com',
        ]);
        $client->request($httpMethod, $url);
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful(sprintf('The %s private URL loads correctly.', $url));
    }

    public function getPrivateUrls(): ?\Generator
    {
        yield ['GET', '/language-test'];
    }

    /**
     * @dataProvider getPublicUrls
     * @param string $url
     */
    public function testPublicUrls(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful(sprintf('The %s public URL loads correctly.', $url));
    }

    public function getPublicUrls(): ?\Generator
    {
        yield ['/'];
        yield ['/login'];
    }
}