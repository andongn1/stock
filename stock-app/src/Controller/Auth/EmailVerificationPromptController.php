<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailVerificationPromptController extends Controller
{
    #[Route('/verify-email', name: 'verification.notice', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('auth/verify-email.html.twig');
    }
}
