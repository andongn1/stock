<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmailVerificationPromptController extends AbstractController
{
    #[Route('/verify-email', name: 'verification.notice', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        if ($user->getEmailVerifiedAt() !== null) {
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('auth/verify-email.html.twig', [
            'user' => $user,
        ]);
    }
}
