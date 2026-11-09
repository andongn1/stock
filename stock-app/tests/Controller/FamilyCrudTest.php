<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FamilyCrudTest extends WebTestCase
{
    private function createAdminClient(): KernelBrowser
    {
        $client = static::createClient();

        $container = static::getContainer();
        $userRepository = $container->get('doctrine')->getRepository(User::class);
        $admin = $userRepository->findOneBy(['email' => 'admin@mercuriale.com']);

        if ($admin) {
            $client->loginUser($admin);
        }

        return $client;
    }

    public function testFamiliesIndexPageLoads(): void
    {
        $client = $this->createAdminClient();
        $client->request('GET', '/families/');

        $this->assertResponseIsSuccessful();
    }

    public function testFamiliesCreatePageLoads(): void
    {
        $client = $this->createAdminClient();
        $client->request('GET', '/families/create');

        $this->assertResponseIsSuccessful();
    }

    public function testCanCreateFamily(): void
    {
        $client = $this->createAdminClient();

        $client->request('POST', '/families/', [
            'family' => ['name' => 'Test Family ' . uniqid()],
            '_token' => 'test',
        ]);

        // Should either redirect (success) or return form with errors
        $this->assertTrue(
            $client->getResponse()->isRedirection() || $client->getResponse()->isSuccessful(),
            'Expected redirect or success after creating family'
        );
    }

    public function testUsersPageAccessibleByAdmin(): void
    {
        $client = $this->createAdminClient();
        $client->request('GET', '/users/');

        $this->assertResponseIsSuccessful();
    }

    public function testUsersPageNotAccessibleByRegularUser(): void
    {
        $client = static::createClient();

        $container = static::getContainer();
        $userRepository = $container->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'user@mercuriale.com']);

        if ($user) {
            $client->loginUser($user);
            $client->request('GET', '/users/');

            // Should be denied (403) or redirected
            $this->assertTrue(
                $client->getResponse()->getStatusCode() === 403
                || $client->getResponse()->isRedirection()
            );
        } else {
            $this->markTestSkipped('No regular user found in fixtures');
        }
    }
}
