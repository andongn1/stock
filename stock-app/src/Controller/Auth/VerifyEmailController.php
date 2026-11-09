<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VerifyEmailController extends AbstractController
{
    #[Route('/verify-email/{id}/{hash}', name: 'verification.verify', methods: ['GET'])]
    public function __invoke(Request $request, string $id, string $hash): Response
    {
        // TODO: Implement email verification
        return $this->redirectToRoute('dashboard');
    }
}
