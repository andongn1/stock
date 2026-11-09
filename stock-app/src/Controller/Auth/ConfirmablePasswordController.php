<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConfirmablePasswordController extends AbstractController
{
    #[Route('/confirm-password', name: 'password.confirm', methods: ['GET'])]
    public function show(): Response
    {
        return $this->render('auth/confirm-password.html.twig');
    }

    #[Route('/confirm-password', name: 'password.confirm', methods: ['POST'])]
    public function store(Request $request): Response
    {
        // TODO: Implement password confirmation
        return $this->redirectToRoute('dashboard');
    }
}
