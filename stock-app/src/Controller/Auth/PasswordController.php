<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PasswordController extends AbstractController
{
    #[Route('/password', name: 'password.update', methods: ['PUT'])]
    public function update(Request $request): Response
    {
        // TODO: Implement password update
        return $this->redirectToRoute('dashboard');
    }
}
