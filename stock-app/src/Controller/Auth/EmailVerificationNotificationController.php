<?php

namespace App\Controller\Auth;

use App\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailVerificationNotificationController extends Controller
{
    #[Route('/email/verification-notification', name: 'verification.send', methods: ['POST'])]
    public function store(): Response
    {
        // TODO: Send verification email
        return $this->redirectToRoute('dashboard');
    }
}
