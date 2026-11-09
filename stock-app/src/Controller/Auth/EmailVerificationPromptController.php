<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmailVerificationPromptController extends AbstractController
{
    #[Route('/verify-email', name: 'verification.notice', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        return $this->render('auth/verify-email.html.twig');
    }
}
