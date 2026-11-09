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
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') &&
               str_starts_with($request->headers->get('Authorization'), 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = substr($request->headers->get('Authorization'), 7);
        
        if (empty($apiToken)) {
            throw new BadCredentialsException('No API token provided');
        }

        $token = $this->em->getRepository(PersonalAccessToken::class)->findOneBy(['token' => $apiToken]);
        
        if (!$token || $token->getExpiresAt() && $token->getExpiresAt() < new \DateTime()) {
            throw new BadCredentialsException('Invalid or expired token');
        }

        $user = $this->em->getRepository(User::class)->find($token->getTokenableId());
        
        if (!$user) {
            throw new BadCredentialsException('User not found');
        }

        return new SelfValidatingPassport(new UserBadge($user->getEmail()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = ['message' => strtr($exception->getMessageKey(), $exception->getMessageData())];
        
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
