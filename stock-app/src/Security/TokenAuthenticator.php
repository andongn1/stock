<?php

namespace App\Security;

use App\Entity\PersonalAccessToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function supports(Request $request): ?bool
    {
        // Only support API routes
        return str_starts_with($request->getPathInfo(), '/api/');
    }

    public function authenticate(Request $request): Passport
    {
        $tokenString = $request->headers->get('Authorization');

        if (!$tokenString || strpos($tokenString, 'Bearer ') !== 0) {
            throw new CustomUserMessageAuthenticationException('Token not provided or invalid format. Use Bearer token.');
        }

        $token = substr($tokenString, 7);
        $tokenHash = hash('sha256', $token);

        $tokenRepository = $this->entityManager->getRepository(PersonalAccessToken::class);
        $personalAccessToken = $tokenRepository->findOneBy(['token' => $tokenHash]);

        if (!$personalAccessToken) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        if ($personalAccessToken->getLastUsedAt() === null) {
            $personalAccessToken->setLastUsedAt(new \DateTime());
            $this->entityManager->flush();
        }

        $user = $personalAccessToken->getUser();

        if (!$user || !$user instanceof User) {
            throw new CustomUserMessageAuthenticationException('User not found for token.');
        }

        return new SelfValidatingPassport(new UserBadge($user->getEmail(), function ($email) {
            return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request): Response
    {
        return new JsonResponse(['error' => 'Authentication required'], Response::HTTP_UNAUTHORIZED);
    }
}
