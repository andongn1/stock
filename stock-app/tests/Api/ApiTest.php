<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{
    public function testApiLoginReturnsToken(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'admin@mercuriale.com',
            'password' => 'password',
        ]));

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('user', $response);
        $this->assertEquals('admin@mercuriale.com', $response['user']['email']);
    }

    public function testApiLoginWithInvalidCredentialsReturns401(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'admin@mercuriale.com',
            'password' => 'wrong-password',
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testApiArticlesRequiresToken(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/articles');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testApiArticlesWithTokenReturns200(): void
    {
        $client = static::createClient();

        // First login to get token
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'admin@mercuriale.com',
            'password' => 'password',
        ]));

        $loginResponse = json_decode($client->getResponse()->getContent(), true);
        $token = $loginResponse['token'];

        // Use token to access articles
        $client->request('GET', '/api/articles', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    public function testApiUserEndpoint(): void
    {
        $client = static::createClient();

        // Login
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'admin@mercuriale.com',
            'password' => 'password',
        ]));

        $loginResponse = json_decode($client->getResponse()->getContent(), true);
        $token = $loginResponse['token'];

        // Get user info
        $client->request('GET', '/api/user', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('admin@mercuriale.com', $response['email']);
        $this->assertEquals('admin', $response['role']);
    }
}
