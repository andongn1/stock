<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmailVerificationNotificationController extends AbstractController
{
    #[Route('/email/verification-notification', name: 'verification.send', methods: ['POST'])]
    public function store(Request $request): Response
    {
        // TODO: Implement sending verification email
        return $this->redirectToRoute('dashboard');
    }
}
