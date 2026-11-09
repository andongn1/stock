<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'profile.edit', methods: ['GET'])]
    public function edit(Request $request): Response
    {
        return $this->render('profile/edit.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/', name: 'profile.update', methods: ['PATCH'])]
    public function update(Request $request): Response
    {
        // TODO: Implement profile update
        return $this->redirectToRoute('profile.edit');
    }

    #[Route('/', name: 'profile.destroy', methods: ['DELETE'])]
    public function destroy(Request $request): Response
    {
        // TODO: Implement profile deletion
        return $this->redirectToRoute('login');
    }
}
