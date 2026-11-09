<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NavigationTest extends WebTestCase
{
    private function createAuthenticatedClient(UserRole $role = UserRole::USER): KernelBrowser
    {
        $client = static::createClient();

        $container = static::getContainer();
        $userRepository = $container->get('doctrine')->getRepository(User::class);

        // Find or create a test user
        $user = $userRepository->findOneBy(['email' => 'admin@mercuriale.com']);
        if (!$user) {
            $user = $userRepository->findOneBy(['email' => 'user@mercuriale.com']);
        }

        if ($user) {
            $client->loginUser($user);
        }

        return $client;
    }

    public function testNavigationRendersOnDashboard(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/dashboard');

        $this->assertResponseIsSuccessful();
        // Navigation should contain the app name
        $this->assertSelectorTextContains('nav', 'Stock Management');
    }

    public function testNavigationRendersOnArticles(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/articles/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', 'Articles');
    }

    public function testNavigationRendersOnFamilies(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/families/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', 'Families');
    }

    public function testNavigationRendersOnSuppliers(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/suppliers/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', 'Fournisseurs');
    }

    public function testNavigationRendersOnSubFamilles(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/sub-familles/');

        $this->assertResponseIsSuccessful();
    }
}
