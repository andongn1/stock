<?php

namespace App\Controller\Api;

use App\Entity\PersonalAccessToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class TokenController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('/login', name: 'api.login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], Response::HTTP_UNAUTHORIZED);
        }

        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $data['email']]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        // Generate a random token
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $personalAccessToken = new PersonalAccessToken();
        $personalAccessToken->setName('API Token');
        $personalAccessToken->setToken($tokenHash);
        $personalAccessToken->setUser($user);
        $personalAccessToken->setCreatedAt(new \DateTime());
        $personalAccessToken->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($personalAccessToken);
        $this->entityManager->flush();

        return new JsonResponse([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()->value,
            ],
        ]);
    }

    #[Route('/logout', name: 'api.logout', methods: ['POST'])]
    public function logout(Request $request): Response
    {
        $tokenString = $request->headers->get('Authorization');
        
        if (!$tokenString || strpos($tokenString, 'Bearer ') !== 0) {
            return new JsonResponse(['error' => 'Token not provided'], Response::HTTP_BAD_REQUEST);
        }

        $token = substr($tokenString, 7);
        $tokenHash = hash('sha256', $token);

        $tokenRepository = $this->entityManager->getRepository(PersonalAccessToken::class);
        $personalAccessToken = $tokenRepository->findOneBy(['token' => $tokenHash]);

        if ($personalAccessToken) {
            $this->entityManager->remove($personalAccessToken);
            $this->entityManager->flush();
        }

        return new JsonResponse(['message' => 'Successfully logged out']);
    }

    #[Route('/user', name: 'api.user', methods: ['GET'])]
    public function user(Request $request): Response
    {
        $user = $this->getUser();
        
        if (!$user || !$user instanceof User) {
            return new JsonResponse(['error' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()->value,
            'email_verified_at' => $user->getEmailVerifiedAt() ? $user->getEmailVerifiedAt()->format('Y-m-d H:i:s') : null,
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }
}
