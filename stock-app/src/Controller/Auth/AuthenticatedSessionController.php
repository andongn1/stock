<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticatedSessionController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function create(AuthenticationUtils $authenticationUtils): Response
    {
        // If user is already logged in, redirect to dashboard
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last email entered by the user
        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_email' => $lastEmail,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function destroy(): void
    {
        // This method is handled by the Security system
        // The actual logout is triggered by the security.yaml configuration
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
