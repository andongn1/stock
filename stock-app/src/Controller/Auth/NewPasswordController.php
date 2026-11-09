<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NewPasswordController extends AbstractController
{
    #[Route('/reset-password/{token}', name: 'password.reset', methods: ['GET'])]
    public function create(string $token): Response
    {
        return $this->render('auth/reset-password.html.twig', [
            'token' => $token,
        ]);
    }

    #[Route('/reset-password', name: 'password.store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        // TODO: Implement password reset
        return $this->redirectToRoute('login');
    }
}
