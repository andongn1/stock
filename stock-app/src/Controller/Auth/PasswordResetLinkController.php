<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use App\Form\PasswordFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PasswordResetLinkController extends Controller
{
    #[Route('/forgot-password', name: 'password.request', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('auth/forgot-password.html.twig');
    }

    #[Route('/forgot-password', name: 'password.email', methods: ['POST'])]
    public function store(Request $request): Response
    {
        // TODO: Implement password reset email sending
        // For now, just redirect back
        return $this->redirectToRoute('login');
    }
}
