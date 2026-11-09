<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PasswordResetLinkController extends AbstractController
{
    #[Route('/forgot-password', name: 'password.request', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('auth/forgot-password.html.twig');
    }

    #[Route('/forgot-password', name: 'password.email', methods: ['POST'])]
    public function store(Request $request): Response
    {
        // TODO: Implement password reset link sending
        // For now, just redirect back with a success message
        return $this->redirectToRoute('login');
    }
}
