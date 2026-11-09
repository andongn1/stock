<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
    }

    public function testRegisterPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
    }

    public function testRootRedirectsToLoginWhenNotAuthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects('/login');
    }

    public function testDashboardRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/dashboard');

        $this->assertResponseRedirects('/login');
    }

    public function testForgotPasswordPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/forgot-password');

        $this->assertResponseIsSuccessful();
    }

    public function testUsersPageRequiresAdmin(): void
    {
        $client = static::createClient();
        // Not authenticated - should redirect to login
        $client->request('GET', '/users');

        $this->assertResponseRedirects('/login');
    }
}
