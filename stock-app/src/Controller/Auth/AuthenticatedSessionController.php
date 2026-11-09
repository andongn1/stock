<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Form\LoginFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticatedSessionController extends Controller
{
    #[Route('/login', name: 'login', methods: ['GET'])]
    public function create(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('auth/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/login', name: 'login_check', methods: ['POST'])]
    public function store(Request $request): Response
    {
        // Handled by Symfony security
        return $this->redirectToRoute('dashboard');
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function destroy(): void
    {
        throw new \LogicException('This should be handled by Symfony security.');
    }
}
