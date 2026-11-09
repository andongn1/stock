<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewPasswordController extends Controller
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
